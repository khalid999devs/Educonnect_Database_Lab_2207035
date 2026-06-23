@props(['active' => ''])

<x-navigation.link :href="route('workspace')" :active="$active === 'dashboard'">
    Dashboard
</x-navigation.link>

<x-navigation.link :href="route('catalog.index', ['catalog' => 'resources'])" :active="$active === 'resources'">
    Resources
</x-navigation.link>

<x-navigation.link :href="route('catalog.index', ['catalog' => 'tools'])" :active="$active === 'tools'">
    Tools
</x-navigation.link>

<x-navigation.link :href="route('catalog.index', ['catalog' => 'templates'])" :active="$active === 'templates'">
    Templates
</x-navigation.link>
