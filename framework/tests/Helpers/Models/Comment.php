<?php

declare(strict_types=1);

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Model;

class Comment extends Model
{

    public string $table {
        get => 'comments';
    }

    public string $content {
        get => $this->content;
        set {
            $this->content = $value;
            $this->markPropertyAsChanged('content');
        }
    }

    public string $post_id {
        get => $this->post_id;
        set {
            $this->post_id = $value;
            $this->markPropertyAsChanged('post_id');
        }
    }
}