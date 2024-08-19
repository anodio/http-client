<?php

namespace Anodio\HttpClient;


use Anodio\Core\ContainerStorage;
use DI\Attribute\Inject;

class Http
{
    public static function fake(array $fakes) {
        $container = ContainerStorage::getContainer();
        $container->set('http.fakes', $fakes);
    }

    public static function get(string $url, array $query = []) {
        $container = ContainerStorage::getContainer();
        $client = $container->get(\Illuminate\Http\Client\Factory::class);
        return $client->get($url, $query);
    }
}