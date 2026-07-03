<?php

namespace Database\Seeders;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionReply;
use App\Forum\Discourse\Models\Space;
use App\Forum\Discourse\Models\Topic;
use Illuminate\Database\Seeder;

class ForumDemoSeeder extends Seeder
{
    public function run(): void
    {
        $space = Space::query()->updateOrCreate(
            ['slug' => 'public'],
            [
                'name' => 'Public Forum',
                'description' => 'Öffentlicher Raum für strukturierte Diskussionen.',
                'visibility' => 'public',
            ],
        );

        $energy = $this->topic($space, null, 'Energie', 'energie', 'Diskurse zu Klima, Gebäuden und Versorgung.');
        $buildings = $this->topic($space, $energy, 'Gebäude', 'gebaeude', 'Gebäude, Sanierung und technische Infrastruktur.');
        $heatPumps = $this->topic($space, $buildings, 'Wärmepumpen', 'warmepumpen', 'Effizienz, Kosten und Praxistauglichkeit von Wärmepumpen.');

        $society = $this->topic($space, null, 'Gesellschaft', 'gesellschaft', 'Politische und gesellschaftliche Grundsatzfragen.');
        $feminism = $this->topic($space, $society, 'Feminismus', 'feminismus', 'Gleichstellung, Sprache, Arbeit und Machtstrukturen.');
        $workEquality = $this->topic($space, $feminism, 'Gleichberechtigung im Beruf', 'gleichberechtigung-im-beruf', 'Arbeitswelt, Gehalt, Care-Arbeit und Karrierewege.');

        $pianouniverse = $this->topic($space, null, 'Pianouniverse', 'pianouniverse', 'Produkt- und Community-Diskurse für Pianouniverse.');
        $learning = $this->topic($space, $pianouniverse, 'Lernen', 'lernen', 'Lernwege, Didaktik und Übepraxis.');
        $practice = $this->topic($space, $learning, 'Üben', 'ueben', 'Übemethoden und reflektierte Erfahrungsberichte.');

        $this->discussion(
            $space,
            $heatPumps,
            'Wärmepumpen im Gebäudebestand',
            'waermepumpen-im-gebaeudebestand',
            'Sind Wärmepumpen im Gebäudebestand sinnvoll?',
            'Die Diskussion sammelt frühe Pro- und Contra-Beiträge zu Effizienz, Gebäudezustand und Betriebskosten.',
            'Ein guter Startpunkt ist die Trennung zwischen technischer Effizienz und Wirtschaftlichkeit im konkreten Gebäude.',
        );

        $this->discussion(
            $space,
            $workEquality,
            'Gleichberechtigung im Beruf',
            'gleichberechtigung-im-beruf',
            'Welche Massnahmen verbessern Gleichberechtigung im Beruf am wirksamsten?',
            'Dieses Beispiel bereitet spätere Positionen zu Gehaltstransparenz, Kinderbetreuung und Anti-Bias-Prozessen vor.',
            'Die spätere Struktur sollte normative Ziele und empirische Wirkung sauber trennen.',
        );

        $this->discussion(
            $space,
            $practice,
            'Strukturierte Diskurse in Pianouniverse',
            'strukturierte-diskurse-in-pianouniverse',
            'Soll Pianouniverse strukturierte Diskurse zu Uebemethoden anbieten?',
            'Dieses Thema zeigt, wie das Forum später in Pianouniverse integriert werden kann.',
            'Private Kursräume wären hier eine sinnvolle Ergänzung, während das öffentliche Forum als Referenz sichtbar bleibt.',
        );
    }

    private function topic(Space $space, ?Topic $parent, string $name, string $slug, string $description): Topic
    {
        $path = $parent ? $parent->path.'/'.$slug : $slug;
        $depth = $parent ? $parent->depth + 1 : 0;

        return Topic::query()->updateOrCreate(
            ['space_id' => $space->id, 'path' => $path],
            [
                'parent_id' => $parent?->id,
                'name' => $name,
                'slug' => $slug,
                'path' => $path,
                'depth' => $depth,
                'description' => $description,
            ],
        );
    }

    private function discussion(
        Space $space,
        Topic $topic,
        string $title,
        string $slug,
        string $coreQuestion,
        string $body,
        string $replyBody,
    ): void {
        $discussion = Discussion::query()->updateOrCreate(
            ['topic_id' => $topic->id, 'slug' => $slug],
            [
                'space_id' => $space->id,
                'title' => $title,
                'core_question' => $coreQuestion,
                'body' => $body,
                'status' => 'open',
                'last_replied_at' => now(),
            ],
        );

        DiscussionReply::query()->firstOrCreate(
            ['discussion_id' => $discussion->id, 'body' => $replyBody],
            ['status' => 'visible'],
        );
    }
}
