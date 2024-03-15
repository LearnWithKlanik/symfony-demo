<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Factory;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Post>
 *
 * @method        Post|Proxy                     create(array|callable $attributes = [])
 * @method static Post|Proxy                     createOne(array $attributes = [])
 * @method static Post|Proxy                     find(object|array|mixed $criteria)
 * @method static Post|Proxy                     findOrCreate(array $attributes)
 * @method static Post|Proxy                     first(string $sortedField = 'id')
 * @method static Post|Proxy                     last(string $sortedField = 'id')
 * @method static Post|Proxy                     random(array $attributes = [])
 * @method static Post|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method static Post[]|Proxy[]                 all()
 * @method static Post[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Post[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Post[]|Proxy[]                 findBy(array $attributes)
 * @method static Post[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Post[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Post> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Post> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Post> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Post> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Post> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Post> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Post> random(array $attributes = [])
 * @phpstan-method static Proxy<Post> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Post> repository()
 * @phpstan-method static list<Proxy<Post>> all()
 * @phpstan-method static list<Proxy<Post>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Post>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Post>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Post>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Post>> randomSet(int $number, array $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private SluggerInterface $slugger)
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->title(),
            'content' => self::faker()->text(),
            'publishedAt' => self::faker()->dateTime(),
            'slug' => '',
            'summary' => self::faker()->text(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (Post $post) {
                $post->setSlug($this->slugger->slug($post->getTitle())->lower());
            })
        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
