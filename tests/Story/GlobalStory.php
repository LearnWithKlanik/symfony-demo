<?php

namespace App\Tests\Story;

use App\Entity\User;
use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;
use function Symfony\Component\String\u;

final class GlobalStory extends Story
{
    public function build(): void
    {
        $this->loadUsers();
        $this->loadTags();
        $this->loadPosts();
    }

    private function loadUsers(): void
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $this->addState($username, UserFactory::createOne(
                [
                    'fullname' => $fullname,
                    'username' => $username,
                    'password' => $password,
                    'email' => $email,
                    'roles' => $roles,
                ]
            ));
        }
    }

    private function loadTags(): void
    {
        foreach ($this->getTagData() as $name) {
            $this->addState('tag-'.$name, TagFactory::createOne(
                [
                    'name' => $name,
                ]
            ));
        }
    }

    private function loadPosts(): void
    {
        foreach ($this->getPostData() as [$title, $summary, $content, $publishedAt, $author, $tags]) {
            $this->addState($title, PostFactory::createOne(
                [
                    'title' => $title,
                    'summary' => $summary,
                    'content' => $content,
                    'publishedAt' => $publishedAt,
                    'author' => $author,
                    'tags' => $tags,
                ]
            ));

            CommentFactory::new(
                function ($i) use ($title) {
                    /** @var User $commentAuthor */
                    $commentAuthor = $this->getState('john_user');

                    return [
                        'author' => $commentAuthor,
                        'content' => $this->getRandomText(random_int(255, 512)),
                        'publishedAt' => new \DateTime('now + '.$i.'seconds'),
                        'post' => $this->getState($title),
                    ];
                }
            )->many(1, 5);
        }
    }

    /**
     * @return array<array{string, string, string, string, array<string>}>
     */
    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', [User::ROLE_ADMIN]],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', [User::ROLE_ADMIN]],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', [User::ROLE_USER]],
        ];
    }

    /**
     * @return string[]
     */
    private function getTagData(): array
    {
        return [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    /**
     * @throws \Exception
     *
     * @return array<int, array{0: string, 1: AbstractUnicodeString, 2: string, 3: string, 4: \DateTime, 5: User, 6: array<Tag>}>
     */
    private function getPostData(): array
    {
        $posts = [];

        foreach ($this->getPhrases() as $i => $title) {
            // $postData = [$title, $summary, $content, $publishedAt, $author, $tags, $comments];

            /** @var User $user */
            $user = $this->getState(['jane_admin', 'tom_admin'][0 === $i ? 0 : random_int(0, 1)]);

            $posts[] = [
                $title,
                $this->getRandomText(),
                $this->getPostContent(),
                (new \DateTime('now - '.$i.'days'))->setTime(random_int(8, 17), random_int(7, 49), random_int(0, 59)),
                // Ensure that the first post is written by Jane Doe to simplify tests
                $user,
                $this->getRandomTags(),
            ];
        }

        return $posts;
    }

    /**
     * @return string[]
     */
    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('.');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    private function getPostContent(): string
    {
        return <<<'MARKDOWN'
            Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
            incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
            deserunt mollit anim id est laborum.

              * Ut enim ad minim veniam
              * Quis nostrud exercitation *ullamco laboris*
              * Nisi ut aliquip ex ea commodo consequat

            Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
            nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
            Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
            himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
            luctus dolor.

            Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
            ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
            Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
            efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
            nulla vitae est.

            Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
            sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
            arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
            ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
            tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
            Sed in egestas erat.

            Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
            lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
            congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
            tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
            tincidunt, faucibus nisl in, aliquet libero.
            MARKDOWN;
    }

    /**
     * @throws \Exception
     *
     * @return array<Tag>
     */
    private function getRandomTags(): array
    {
        $tagNames = $this->getTagData();
        shuffle($tagNames);
        $selectedTags = \array_slice($tagNames, 0, random_int(2, 4));

        return array_map(function ($tagName) {
            /** @var Tag $tag */
            $tag = $this->getState('tag-'.$tagName);

            return $tag;
        }, $selectedTags);
    }
}
