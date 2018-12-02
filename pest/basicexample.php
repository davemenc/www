<?php
require 'Pest.php';

$pest = new Pest('http://example.com');

$thing = $pest->get('/things');

$thing = $pest->post('/things', 
    array(
        'name' => "Foo",
        'colour' => "Red"
    )
);

$thing = $pest->put('/things/15',
    array(
        'colour' => "Blue"
    )
);

$pest->delete('/things/15');

?>