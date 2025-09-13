<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="DuAnTotNghiep API", version="1.0.0", description="REST API documentation for Users and other resources")
 * @OA\Server(url="/duan/duan/duantotnghiep/public", description="Localhost base path")
 * @OA\Tag(name="1. Location", description="Location management endpoints")
 * @OA\Tag(name="2. Users", description="User management endpoints") 
 * @OA\Tag(name="3. Categories", description="Category management endpoints")
 * @OA\Tag(name="4. Products", description="Product management endpoints")
 * @OA\Tag(name="5. Orders", description="Order management endpoints")
 * @OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="http://swagger.io"
 * )
 */
class OpenApi {}

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
 *   @OA\Property(property="email", type="string", format="email", example="a@example.com"),
 *   @OA\Property(property="phone", type="string", example="0901234567"),
 *   @OA\Property(property="image", type="string", nullable=true, example="https://.../avatar.jpg"),
 *   @OA\Property(property="status", type="integer", enum={0,1}, example=1),
 *   @OA\Property(property="role", type="integer", enum={1,2}, example=2),
 *   @OA\Property(property="province_id", type="integer", nullable=true, example=79),
 *   @OA\Property(property="district_id", type="integer", nullable=true, example=760),
 *   @OA\Property(property="ward_id", type="integer", nullable=true, example=26734),
 *   @OA\Property(property="address", type="string", nullable=true, example="123 ABC, Q1, HCM"),
 *   @OA\Property(property="birthday", type="string", format="date", nullable=true, example="2000-01-01"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Ghi chú"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UserSchema {}

/**
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="per_page", type="integer", example=10),
 *   @OA\Property(property="total", type="integer", example=120),
 *   @OA\Property(property="last_page", type="integer", example=12)
 * )
 */
class PaginationMetaSchema {}


