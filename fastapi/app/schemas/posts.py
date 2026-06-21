from pydantic import BaseModel
from datetime import datetime
from .common import UserSchema, CategorySchema

class CommentSchema(BaseModel):
    id: int
    user: UserSchema
    content: str
    is_deleted: bool
    parent_id: int | None
    created_at: datetime

    model_config = {"from_attributes": True}

class PostSchema(BaseModel):
    id: int
    title: str | None
    content: str | None
    content_type: str
    excerpt: str | None
    status: str
    published_at: datetime | None
    created_at: datetime
    user: UserSchema
    category: CategorySchema
    likes: int = 0
    dislikes: int = 0

    model_config = {"from_attributes": True}

class PostListSchema(BaseModel):
    items: list[PostSchema]
    total: int
    page: int
    size: int
    pages: int