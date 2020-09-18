<?php

namespace App\Models;

use App\User;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $anons
 * @property string|null $content
 * @property int $publish
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAnons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @mixin \Eloquent
 */
class Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'files' =>'json',
    ];

    private $files = [
        'doc1',
        'doc2'
    ];

    /**
     * Связь с моделью User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Administrator::class,'author_id','id');
    }

    /**
     * Связь с моделью Comment
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class,'post_id','id');
    }

    public function getFilesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setFilesAttribute($value)
    {
        $files = null;
        $originalValue = $this->getOriginal()['files'];
        foreach ($this->files as $file) {
            if (isset($value[$file])) {
                $files[$file] = $value[$file];
            } elseif (isset($originalValue[$file])) {
                $files[$file] = $originalValue[$file];
            }
        }
        $this->attributes['files'] = json_encode($files);
    }
}