import json
from redis.asyncio import Redis
from .manager import manager

CHANNELS = ["post.created", "post.updated", "post.deleted", "comment.created"]

async def redis_listener(redis: Redis):
    pubsub = redis.pubsub()
    await pubsub.subscribe(*CHANNELS)
    
    async for message in pubsub.listen():
        if message["type"] != "message":
            continue
        
        try:
            data = json.loads(message["data"])
        except (json.JSONDecodeError, TypeError):
            continue
        
        event = data.get("event")
        subscriber_ids = data.get("subscriber_ids", [])
        
        if subscriber_ids:
            for user_id in subscriber_ids:
                if user_id in manager.active_user_ids():
                    await manager.send_to_user(user_id, data)
        else:
            await manager.broadcast(data)