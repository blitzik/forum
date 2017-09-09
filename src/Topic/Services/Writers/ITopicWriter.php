<?php declare(strict_types = 1);

namespace Topic\Services\Writers;

use Topic\Exceptions\TopicCreationFailedException;
use Category\Category;
use Account\Account;
use Topic\Topic;

interface ITopicWriter
{
    /**
     * @param Account $author
     * @param Category $category
     * @param string $title
     * @param string $text
     * @return Topic
     * @throws TopicCreationFailedException
     */
    public function write(Account $author, Category $category, string $title, string $text): Topic;
}