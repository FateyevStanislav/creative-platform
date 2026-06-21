from sqlalchemy.ext.asyncio import create_async_engine, async_sessionmaker, AsyncSession
from sqlalchemy.orm import DeclarativeBase
import os

DB_HOST = os.getenv("DB_HOST", "mysql")
DB_DATABASE = os.getenv("DB_DATABASE", "creative")
DB_USERNAME = os.getenv("DB_USERNAME", "creative")
DB_PASSWORD = os.getenv("DB_PASSWORD", "secret")

DATABASE_URL = f"mysql+aiomysql://{DB_USERNAME}:{DB_PASSWORD}@{DB_HOST}/{DB_DATABASE}"

engine = create_async_engine(DATABASE_URL, echo=False)
AsyncSessionLocal = async_sessionmaker(engine, expire_on_commit=False)

class Base(DeclarativeBase):
    pass

async def get_db() -> AsyncSession:
    async with AsyncSessionLocal() as session:
        yield session