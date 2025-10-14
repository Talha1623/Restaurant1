# Restaurant Profile Update API

## Endpoint
**PUT** `/api/restaurants/update-profile`

## Authentication
Requires Bearer token authentication. Restaurant must be logged in.

**Headers:**
```
Authorization: Bearer {your-token}
Content-Type: application/json (for JSON data)
Content-Type: multipart/form-data (for file uploads)
```

## Description
Allows authenticated restaurants to update their own profile information including all fields and file uploads.

## Request Body
All fields are optional. Only include the fields you want to update.

### JSON Fields:
```json
{
    "legal_name": "Updated Legal Name",
    "business_name": "Updated Business Name",
    "email": "newemail@restaurant.com",
    "phone": "+1234567890",
    "contact_person": "John Doe",
    "address_line1": "123 Main Street",
    "city": "London",
    "postcode": "SW1A 1AA",
    "opening_time": "09:00",
    "closing_time": "22:00",
    "min_order": 15.00,
    "status": "active",
    "cuisine_tags": "Italian, Pizza, Vegan",
    "delivery_zone": "5",
    "delivery_postcode": "SW1A 1AA",
    "restaurant_password": "newpassword123"
}
```

### File Upload Fields:
- `logo`: Restaurant logo image (optional, max 2MB)
- `banner`: Restaurant banner image (optional, max 2MB)

## Response

### Success Response (200):
```json
{
    "success": true,
    "message": "Restaurant profile updated successfully",
    "data": {
        "restaurant": {
            "id": 1,
            "legal_name": "Updated Legal Name",
            "business_name": "Updated Business Name",
            "email": "newemail@restaurant.com",
            "phone": "+1234567890",
            "contact_person": "John Doe",
            "address_line1": "123 Main Street",
            "city": "London",
            "postcode": "SW1A 1AA",
            "opening_time": "09:00",
            "closing_time": "22:00",
            "min_order": 15.00,
            "status": "active",
            "cuisine_tags": "Italian, Pizza, Vegan",
            "delivery_zone": "5",
            "delivery_postcode": "SW1A 1AA",
            "logo": "http://localhost:8000/storage/restaurant-logos/filename.jpg",
            "banner": "http://localhost:8000/storage/restaurant-banners/filename.jpg",
            "created_at": "2025-01-20T10:00:00.000000Z",
            "updated_at": "2025-01-20T15:30:00.000000Z"
        }
    }
}
```

### Error Responses:

#### 401 Unauthorized:
```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

#### 422 Validation Error:
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email has already been taken."],
        "phone": ["The phone field is required."]
    }
}
```

#### 500 Server Error:
```json
{
    "success": false,
    "message": "Server error: Error message here"
}
```

## Usage Examples

### 1. Update Basic Information:
```bash
curl -X PUT http://127.0.0.1:8000/api/restaurants/update-profile \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "business_name": "New Restaurant Name",
    "phone": "+1234567890",
    "min_order": 20.00
  }'
```

### 2. Update with File Upload:
```bash
curl -X PUT http://127.0.0.1:8000/api/restaurants/update-profile \
  -H "Authorization: Bearer {your-token}" \
  -F "business_name=New Name" \
  -F "logo=@/path/to/new-logo.jpg" \
  -F "banner=@/path/to/new-banner.jpg"
```

### 3. Update Password:
```bash
curl -X PUT http://127.0.0.1:8000/api/restaurants/update-profile \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_password": "newpassword123"
  }'
```

## Features

✅ **All Fields Supported**: Update any restaurant field
✅ **File Upload**: Logo and banner image upload
✅ **Password Update**: Secure password change
✅ **Validation**: Complete field validation
✅ **Old File Cleanup**: Automatically deletes old images
✅ **Authentication**: Secure with Bearer token
✅ **Flexible**: Update only the fields you need

## Notes

1. **Authentication Required**: Restaurant must be logged in
2. **File Upload**: Use `multipart/form-data` for file uploads
3. **Image Formats**: Supports JPEG, PNG, JPG, GIF (max 2MB)
4. **Old Files**: Previous logo/banner files are automatically deleted
5. **Password**: Use `restaurant_password` field (not `password`)
6. **Partial Updates**: Only send fields you want to update
7. **Email Uniqueness**: Email must be unique across all restaurants

## Related Endpoints

- `POST /api/restaurants/register` - Register new restaurant
- `POST /api/restaurants/login` - Restaurant login
- `GET /api/restaurants/profile` - Get current restaurant profile
- `POST /api/restaurants/logout` - Restaurant logout
