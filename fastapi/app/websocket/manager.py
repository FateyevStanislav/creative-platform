from fastapi import WebSocket
from collections import defaultdict

class ConnectionManager:
    def __init__(self):
        self.connections: dict[int, list[WebSocket]] = defaultdict(list)

    async def connect(self, websocket: WebSocket, user_id: int):
        await websocket.accept()
        self.connections[user_id].append(websocket)

    def disconnect(self, websocket: WebSocket, user_id: int):
        if user_id in self.connections:
            self.connections[user_id].remove(websocket)
            if not self.connections[user_id]:
                del self.connections[user_id]

    async def send_to_user(self, user_id: int, data: dict):
        for ws in self.connections.get(user_id, []):
            await ws.send_json(data)

    async def broadcast(self, data: dict):
        for user_id, sockets in self.connections.items():
            for ws in sockets:
                await ws.send_json(data)

    def active_user_ids(self) -> set[int]:
        return set(self.connections.keys())

manager = ConnectionManager()