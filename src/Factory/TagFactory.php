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

use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Tag>
 *
 * @method        Tag|Proxy                        create(array|callable $attributes = [])
 * @method static Tag|Proxy                        createOne(array $attributes = [])
 * @method static Tag|Proxy                        find(object|array|mixed $criteria)
 * @method static Tag|Proxy                        findOrCreate(array $attributes)
 * @method static Tag|Proxy                        first(string $sortedField = 'id')
 * @method static Tag|Proxy                        last(string $sortedField = 'id')
 * @method static Tag|Proxy                        random(array $attributes = [])
 * @method static Tag|Proxy                        randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Tag[]|Proxy[]                    all()
 * @method static Tag[]|Proxy[]                    createMany(int $number, array|callable $attributes = [])
 * @method static Tag[]|Proxy[]                    createSequence(iterable|callable $sequence)
 * @method static Tag[]|Proxy[]                    findBy(array $attributes)
 * @method static Tag[]|Proxy[]                    randomRange(int $min, int $max, array $attributes = [])
 * @method static Tag[]|Proxy[]                    randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Tag> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Tag> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Tag> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Tag> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Tag> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Tag> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Tag> random(array $attributes = [])
 * @phpstan-method static Proxy<Tag> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Tag> repository()
 * @phpstan-method static list<Proxy<Tag>> all()
 * @phpstan-method static list<Proxy<Tag>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Tag>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Tag>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Tag>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Tag>> randomSet(int $number, array $attributes = [])
 */
final class TagFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
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
            'name' => self::faker()->unique()->word(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Tag $tag): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Tag::class;
    }
}
