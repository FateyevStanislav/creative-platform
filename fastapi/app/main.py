import asyncio
import os
from contextlib import asynccontextmanager
from fastapi import FastAPI, WebSocket, WebSocketDisconnect, Query
from redis.asyncio import Redis
from .api.posts import router as posts_router
from .websocket.manager import manager
from .websocket.redis_listener import redis_listener

REDIS_HOST = os.getenv("REDIS_HOST", "redis")


@asynccontextmanager
async def lifespan(app: FastAPI):
    redis = Redis(host=REDIS_HOST, decode_responses=True)
    task = asyncio.create_task(redis_listener(redis))
    yield
    task.cancel()
    await redis.aclose()


app = FastAPI(lifespan=lifespan)
app.include_router(posts_router)


@app.get("/api/health")
async def health():
    return {"status": "ok"}


@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket, user_id: int = Query(...)):
    await manager.connect(websocket, user_id)
    try:
        while True:
            await websocket.receive_text()
    except WebSocketDisconnect:
        manager.disconnect(websocket, user_id)