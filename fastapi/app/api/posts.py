from fastapi import APIRouter, Depends, Query, HTTPException
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func
from sqlalchemy.orm import selectinload
from ..db.database import get_db
from ..db.models import Post, Category, Reaction, Subscription, User
from ..schemas.posts import PostSchema, PostListSchema
from ..schemas.common import CategorySchema

router = APIRouter()


async def enrich_post(post, db):
    likes = await db.scalar(select(func.count()).where(Reaction.post_id == post.id, Reaction.type == "like"))
    dislikes = await db.scalar(select(func.count()).where(Reaction.post_id == post.id, Reaction.type == "dislike"))
    result = PostSchema.model_validate(post)
    result.likes = likes or 0
    result.dislikes = dislikes or 0
    return result


def post_query_with_relations():
    return select(Post).options(
        selectinload(Post.user).selectinload(User.role),
        selectinload(Post.category)
    )


@router.get("/api/posts", response_model=PostListSchema)
async def get_posts(
    page: int = Query(1, ge=1),
    size: int = Query(10, ge=1, le=100),
    category: str | None = None,
    publisher_id: int | None = None,
    content_type: str | None = None,
    db: AsyncSession = Depends(get_db),
):
    q = post_query_with_relations().where(Post.status == "published")

    if category:
        cat = await db.scalar(select(Category).where(Category.slug == category))
        if cat:
            q = q.where(Post.category_id == cat.id)

    if publisher_id:
        q = q.where(Post.user_id == publisher_id)

    if content_type:
        q = q.where(Post.content_type == content_type)

    total = await db.scalar(select(func.count()).select_from(q.subquery()))
    posts = (await db.execute(
        q.order_by(Post.published_at.desc()).offset((page - 1) * size).limit(size)
    )).scalars().all()

    items = [await enrich_post(p, db) for p in posts]
    return PostListSchema(items=items, total=total, page=page, size=size, pages=-(-total // size))


@router.get("/api/posts/{post_id}", response_model=PostSchema)
async def get_post(post_id: int, db: AsyncSession = Depends(get_db)):
    post = (await db.execute(
        post_query_with_relations().where(Post.id == post_id, Post.status == "published")
    )).scalar_one_or_none()

    if not post:
        raise HTTPException(status_code=404, detail="Post not found")

    return await enrich_post(post, db)


@router.get("/api/categories", response_model=list[CategorySchema])
async def get_categories(db: AsyncSession = Depends(get_db)):
    cats = (await db.execute(select(Category).where(Category.is_active == True))).scalars().all()
    return cats


@router.get("/api/categories/{slug}/posts", response_model=PostListSchema)
async def get_posts_by_category(slug: str, page: int = 1, size: int = 10, db: AsyncSession = Depends(get_db)):
    return await get_posts(page=page, size=size, category=slug, db=db)


@router.get("/api/publishers/{publisher_id}/posts", response_model=PostListSchema)
async def get_publisher_posts(publisher_id: int, page: int = 1, size: int = 10, db: AsyncSession = Depends(get_db)):
    return await get_posts(page=page, size=size, publisher_id=publisher_id, db=db)


@router.get("/api/feed/subscriptions/{user_id}", response_model=PostListSchema)
async def get_subscription_feed(user_id: int, page: int = 1, size: int = 10, db: AsyncSession = Depends(get_db)):
    publisher_ids = (await db.execute(
        select(Subscription.publisher_id).where(Subscription.subscriber_id == user_id)
    )).scalars().all()

    q = post_query_with_relations().where(Post.status == "published", Post.user_id.in_(publisher_ids))
    total = await db.scalar(select(func.count()).select_from(q.subquery()))
    posts = (await db.execute(
        q.order_by(Post.published_at.desc()).offset((page - 1) * size).limit(size)
    )).scalars().all()

    items = [await enrich_post(p, db) for p in posts]
    return PostListSchema(items=items, total=total, page=page, size=size, pages=-(-total // size))