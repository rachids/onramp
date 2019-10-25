<?php

namespace App;

use Exception;

class Preferences
{
    protected $user;

    protected $preferences = [
        'resource-language-preference' => [
            'options' => [
                'local' => 'Only local resources',
                'all' => 'All resoures',
                'local-and-english' => 'Only local and English resources',
            ],
            'default' => 'local',
        ],
    ];

    public function __construct(User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    public function set(array $array)
    {
        $this->checkUser();
        $this->checkKeys(array_keys($array));

        $this->user->update([
            'preferences' => array_merge((array) $this->user->preferences, $array),
        ]);
    }

    public function get($key, $default = null)
    {
        $this->checkUser();
        $this->checkKeys([$key]);

        return data_get(
            $this->user->preferences,
            $key,
            $this->defaultForKey($key, $default)
        );
    }

    public function defaultForKey($key, $defaultOverride = null)
    {
        return $defaultOverride ?? $this->preferences[$key]['default'];
    }

    protected function checkUser()
    {
        if (! $this->user) {
            throw new Exception('Cannot use Preferences method for un-logged-in users.');
        }
    }

    protected function checkKeys($keys)
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $this->preferences)) {
                throw new Exception('Preference key ' . $key . ' not defined.');
            }
        }
    }
}
