<?php

namespace App\Service;

use App\Entity\Comment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    private $endpoint;

    public function __construct(
        private LocaleSwitcher $localeSwitcher,
        private HttpClientInterface $client,
        #[Autowire('%env(AKISMET_KEY)%')] string $akismetKey,
    ) {
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    /**
     * @throws \RuntimeException if the call did not work
     *
     * @return int Spam score: 0: not spam, 1: maybe spam, 2: blatant spam
     */
    public function getSpamScore(Comment $comment, array $context): int
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
                'blog' => 'https://guestbook.example.com',
                'comment_type' => 'comment',
                'comment_author' => $comment->getAuthor()->getFullName(),
                'comment_author_email' => $comment->getAuthor()->getEmail(),
                'comment_content' => $comment->getContent(),
                'comment_date_gmt' => $comment->getPublishedAt()->format('c'),
                'blog_lang' => $this->localeSwitcher->getLocale(),
                'blog_charset' => 'UTF-8',
                'is_test' => true,
            ]),
        ]);

        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new \RuntimeException(sprintf('Unable to check for spam: %s (%s).', $content, $headers['x-akismet-debug-help'][0]));
        }

        return 'true' === $content ? 1 : 0;
    }
}
