<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Model;

use App\Components\FileAdapter;
use Hyperf\Scout\Searchable;

/**
 * @property int $id
 * @property int $uid
 * @property string $title
 * @property string $content_path
 * @property string $content_hash
 * @property int $create_time
 * @property int $update_time
 */
class Question extends Model
{
    use Searchable;

    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = 'update_time';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'question';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['uid', 'title', 'content_path', 'content_hash', 'create_time', 'update_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'create_time' => 'integer', 'update_time' => 'integer'];

    /*
     * 配置可搜索的数据
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $contentFlag = false;
        $searchRaw = Question::search()->where('id', $array['id'])->raw();
        if (! empty($searchRaw)) {
            $searchHit = $searchRaw['hits']['hits'] ?? [];
            // 这里要避免不必要的读
            if (! $searchHit || (! empty($searchHit) && $searchHit[0]['_source']['content_hash'] != $array['content_hash'])) {
                $contentFlag = true;
            }
        } else {
            $contentFlag = true;
        }

        if ($contentFlag && ! empty($array['content_path'])) {
            $adapter = make(FileAdapter::class);
            $contentUrl = $adapter->privateDownloadUrl($array['content_path']);
            $array['content'] = $adapter->read($contentUrl);
        }

        unset($array['content_path']);

        return $array;
    }
}
