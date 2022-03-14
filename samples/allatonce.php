<?php

require_once __DIR__.'/../vendor/autoload.php';


$client = new \BABA\Collabim\API\Client\Collabim();
$client->authenticate();


try {
    foreach ($client->projectsGetList() as $project) {
        echo "HTML Widget for {$project->id}\n";
        echo $client->projectGetWidgetsHTML($project->id);
        echo "Widget for {$project->id}\n";
        var_dump($client->projectGetWidgets($project->id));
        echo "Keywords for {$project->id}\n";
        var_dump($client->projectGetKeywords($project->id));
        echo "Keywords position for {$project->id}\n";
        var_dump($client->projectGetKeywordsPosition($project->id));
        echo "Keywords aggregated position for {$project->id}\n";
        var_dump($client->projectGetKeywordsAggregatedPosition($project->id));
        echo "Position distribution for {$project->id}\n";
        var_dump($client->projectGetPositionDistribution($project->id));
        echo "Project indexed pages for {$project->id}\n";
        var_dump($client->projectGetIndexedPages($project->id));
        echo "Project market share for {$project->id}\n";
        var_dump($client->projectGetMarketShare($project->id));
        echo "Project activities for {$project->id}\n";
        var_dump($client->projectGetActivities($project->id));
        echo "Project INFO for {$project->id}\n";
        $client->projectGetInfoById($project->id);
    }
} catch(\Exception $e) {
    die($e->getMessage());
}