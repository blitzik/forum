<?php declare(strict_types = 1);

namespace Post\Services\Writers;

use Account\Account;
use Topic\Topic;
use Post\Post;

interface IPostWriter
{
    public function write(Account $author, Topic $topic, string $text): Post;
}