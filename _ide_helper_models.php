<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $subject
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUpdatedAt($value)
 */
	class Contact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string|null $cv_path
 * @property string|null $image_path
 * @property int $id
 * @property string|null $heading
 * @property array<array-key, mixed>|null $subheading
 * @property string|null $description
 * @property string|null $primary_btn_text
 * @property string|null $primary_btn_link
 * @property string|null $secondary_btn_text
 * @property array<array-key, mixed>|null $file
 * @property array<array-key, mixed>|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereGreeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent wherePrimaryBtnLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent wherePrimaryBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereSecondaryBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeContent whereUpdatedAt($value)
 */
	class HomeContent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

