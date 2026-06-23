from sqlalchemy import Column, Integer, String, Text, Boolean, DateTime, ForeignKey, Enum
from sqlalchemy.orm import relationship
from .database import Base

class Role(Base):
    __tablename__ = "roles"
    id = Column(Integer, primary_key=True)
    name = Column(String(50), unique=True)
    display_name = Column(String(100))

class User(Base):
    __tablename__ = "users"
    id = Column(Integer, primary_key=True)
    name = Column(String(255))
    email = Column(String(255), unique=True, nullable=True)
    role_id = Column(Integer, ForeignKey("roles.id"))
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime)
    role = relationship("Role")

class Category(Base):
    __tablename__ = "categories"
    id = Column(Integer, primary_key=True)
    name = Column(String(255))
    slug = Column(String(255), unique=True)
    description = Column(Text, nullable=True)
    is_active = Column(Boolean, default=True)

class Post(Base):
    __tablename__ = "posts"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    category_id = Column(Integer, ForeignKey("categories.id"))
    title = Column(String(255), nullable=True)
    content = Column(Text, nullable=True)
    content_type = Column(Enum("text", "image", "audio", "mixed"), default="text")
    excerpt = Column(Text, nullable=True)
    status = Column(Enum("draft", "published", "deleted"), default="published")
    published_at = Column(DateTime, nullable=True)
    created_at = Column(DateTime)
    user = relationship("User")
    category = relationship("Category")
    reactions = relationship("Reaction", back_populates="post")
    comments = relationship("Comment", back_populates="post")
    media_path = Column(String(255), nullable=True)
    updated_at = Column(DateTime, nullable=True)    

class Comment(Base):
    __tablename__ = "comments"
    id = Column(Integer, primary_key=True)
    post_id = Column(Integer, ForeignKey("posts.id"))
    user_id = Column(Integer, ForeignKey("users.id"))
    parent_id = Column(Integer, ForeignKey("comments.id"), nullable=True)
    content = Column(Text)
    is_deleted = Column(Boolean, default=False)
    created_at = Column(DateTime)
    user = relationship("User")
    post = relationship("Post", back_populates="comments")
    replies = relationship("Comment")

class Reaction(Base):
    __tablename__ = "reactions"
    id = Column(Integer, primary_key=True)
    post_id = Column(Integer, ForeignKey("posts.id"))
    user_id = Column(Integer, ForeignKey("users.id"))
    type = Column(Enum("like", "dislike"))
    post = relationship("Post", back_populates="reactions")

class Subscription(Base):
    __tablename__ = "subscriptions"
    id = Column(Integer, primary_key=True)
    subscriber_id = Column(Integer, ForeignKey("users.id"))
    publisher_id = Column(Integer, ForeignKey("users.id"))

class Report(Base):
    __tablename__ = "reports"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    target_type = Column(Enum("post", "comment", "user"))
    target_id = Column(Integer)
    reason = Column(String(50))
    status = Column(Enum("pending", "reviewed", "rejected", "accepted"), default="pending")
    created_at = Column(DateTime)
    message = Column(Text, nullable=True)
    reviewed_by = Column(Integer, ForeignKey("users.id"), nullable=True)
    reviewed_at = Column(DateTime, nullable=True)
    updated_at = Column(DateTime, nullable=True)