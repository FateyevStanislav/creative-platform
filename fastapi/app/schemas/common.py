from pydantic import BaseModel
from datetime import datetime

class RoleSchema(BaseModel):
    id: int
    name: str
    display_name: str

    model_config = {"from_attributes": True}

class UserSchema(BaseModel):
    id: int
    name: str
    role: RoleSchema

    model_config = {"from_attributes": True}

class CategorySchema(BaseModel):
    id: int
    name: str
    slug: str
    description: str | None

    model_config = {"from_attributes": True}