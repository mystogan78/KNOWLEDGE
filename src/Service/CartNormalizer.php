<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Structure d’entrée des items (exemple côté contrôleur) :
 * $items = [
 *   ['type' => 'course', 'courseId' => 10],
 *   ['type' => 'lesson', 'lessonId' => 55],
 *   ...
 * ];
 *
 * Sortie :
 * [
 *   'items' => [
 *      ['type'=>'course','courseId'=>10,'amount'=>'50.00'],
 *      ['type'=>'lesson','lessonId'=>55,'courseId'=>10,'amount'=>'26.00'],
 *   ],
 *   'upsell' => [
 *      ['courseId'=>10, 'lessonIds'=>[55,56], 'lessonSum'=>'52.00', 'coursePrice'=>'50.00', 'suggest'=>true]
 *   ]
 * ]
 */
class CartNormalizer
{
    public function __construct(
        private EntityManagerInterface $em,
        private CourseRepository $courseRepo,
        private LessonRepository $lessonRepo,
        private PurchaseRepository $purchaseRepo // fournit hasCourse/hasLesson
    ) {}

    /** marge en euros pour déclencher l’upsell vers le cours */
    private const UPSELL_MARGIN = 1.00; // par ex. déclenche si somme leçons >= coursePrice - 1 €

    public function normalize(User $user, array $rawItems): array
    {
        // 1) Charger entités + transformer en items enrichis (et valider)
        $loaded = [];
        foreach ($rawItems as $it) {
            if (($it['type'] ?? '') === 'course' && isset($it['courseId'])) {
                $course = $this->courseRepo->find((int)$it['courseId']);
                if (!$course) { continue; }
                $loaded[] = [
                    'type' => 'course',
                    'courseId' => $course->getId(),
                    'amount' => (string)$course->getPrice(),
                ];
            } elseif (($it['type'] ?? '') === 'lesson' && isset($it['lessonId'])) {
                $lesson = $this->lessonRepo->find((int)$it['lessonId']);
                if (!$lesson) { continue; }
                $loaded[] = [
                    'type' => 'lesson',
                    'lessonId' => $lesson->getId(),
                    'courseId' => $lesson->getCourse()->getId(),
                    'amount' => (string)$lesson->getPrice(),
                ];
            }
        }

        if (!$loaded) return ['items' => [], 'upsell' => []];

        // 2) Déduplication
        $unique = [];
        $seen = [];
        foreach ($loaded as $it) {
            $key = $it['type'] . ':' . ($it['courseId'] ?? '-') . ':' . ($it['lessonId'] ?? '-');
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $it;
            }
        }

        // 3) Retirer les leçons couvertes par un cours déjà dans le panier
        $courseIdsInCart = array_column(array_filter($unique, fn($i)=>$i['type']==='course'), 'courseId');
        if ($courseIdsInCart) {
            $unique = array_values(array_filter($unique, function($i) use ($courseIdsInCart) {
                if ($i['type'] === 'lesson' && in_array($i['courseId'], $courseIdsInCart, true)) {
                    return false;
                }
                return true;
            }));
        }

        // 4) Retirer les leçons d’un cours que l’utilisateur possède déjà
        if ($unique) {
            $unique = array_values(array_filter($unique, function($i) use ($user) {
                if ($i['type'] === 'lesson') {
                    return !$this->purchaseRepo->hasCourse($user, $i['courseId']);
                }
                return true;
            }));
        }

        // 5) Upsell suggestions par cours
        $upsell = [];
        // Regrouper les leçons par course
        $lessonsByCourse = [];
        foreach ($unique as $i) {
            if ($i['type'] === 'lesson') {
                $lessonsByCourse[$i['courseId']][] = $i;
            }
        }

        foreach ($lessonsByCourse as $courseId => $lessons) {
            // si le course est déjà dans le panier → pas d’upsell
            if (in_array($courseId, $courseIdsInCart, true)) {
                continue;
            }

            $course = $this->courseRepo->find($courseId);
            if (!$course) { continue; }

            // somme des prix des leçons sélectionnées
            $sum = array_reduce($lessons, fn($c,$l)=>$c + (float)$l['amount'], 0.0);
            $coursePrice = (float)$course->getPrice();

            $suggest = ($sum >= ($coursePrice - self::UPSELL_MARGIN)) && count($lessons) >= 2;

            $upsell[] = [
                'courseId'    => $courseId,
                'lessonIds'   => array_column($lessons, 'lessonId'),
                'lessonSum'   => number_format($sum, 2, '.', ''),
                'coursePrice' => number_format($coursePrice, 2, '.', ''),
                'suggest'     => $suggest
            ];
        }

        return [
            'items'  => $unique,
            'upsell' => $upsell
        ];
    }
}
