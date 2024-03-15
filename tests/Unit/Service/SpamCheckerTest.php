<?php

namespace App\Tests\Unit\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Service\SpamChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpamCheckerTest extends TestCase
{
    public function testSpamScoreWithInvalidRequest(): void
    {
        // Arrange
        $comment = new Comment();
        $comment->setAuthor(new User());
        $context = [];

        $client = new MockHttpClient([new MockResponse('invalid', ['response_headers' => ['x-akismet-debug-help: Invalid key']])]);
        $checker = new SpamChecker($this->getLocaleSwitcherStub(), $client, 'abcde');

        // Expect
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');

        // Act
        $checker->getSpamScore($comment, $context);
    }

    /**
     * @dataProvider provideComments
     */
    public function testSpamScore(int $expectedScore, ResponseInterface $response, Comment $comment, array $context)
    {
        // Arrange
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($this->getLocaleSwitcherStub(), $client, 'abcde');

        // Act
        $score = $checker->getSpamScore($comment, $context);

        // Assert
        $this->assertSame($expectedScore, $score);
    }

    public static function provideComments(): iterable
    {
            $comment = new Comment();
            $comment->setAuthor(new User());
            $context = [];

            $response = new MockResponse('', ['response_headers' => ['x-akismet-pro-tip: discard']]);
            yield 'blatant_spam' => [2, $response, $comment, $context];

            $response = new MockResponse('true');
            yield 'spam' => [1, $response, $comment, $context];

            $response = new MockResponse('false');
            yield 'ham' => [0, $response, $comment, $context];
        }

    private function getLocaleSwitcherStub($locale = 'en')
    {
        /** @var LocaleSwitcher|MockObject $localeSwitcherStub */
        $localeSwitcherStub = static::getMockBuilder(LocaleSwitcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $localeSwitcherStub->method('getLocale')
            ->willReturn($locale);

        return $localeSwitcherStub;
    }
}