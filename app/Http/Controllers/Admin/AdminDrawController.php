<?php

namespace App\Http\Controllers\Admin;

use App\Models\Draw;
use App\Models\DrawWinner;
use Illuminate\Http\Request;
use View;

/**
 * Page admin : SYSTÈME DE TIRAGE (Denis/Steve 05.08).
 * Trois bassins : membres clients, membres fournisseurs, ou les deux.
 * Le tirage est aléatoire parmi les comptes ACTIFS du bassin; les gagnants sont
 * figés (numéro de membre, nom, courriel) et conservés dans l'historique.
 */
class AdminDrawController extends AdminBaseController
{
    public function index()
    {
        $pools = Draw::pools();
        $counts = [];
        foreach (array_keys($pools) as $pool) {
            $counts[$pool] = Draw::eligibleQuery($pool)->count();
        }

        $draws = Draw::with('winners')->orderByDesc('id')->limit(50)->get();

        return View::make('draw.index', compact('pools', 'counts', 'draws'));
    }

    public function run(Request $request)
    {
        $pools = array_keys(Draw::pools());
        $pool = in_array($request->input('pool'), $pools, true) ? $request->input('pool') : Draw::POOL_BOTH;
        $winnersCount = max(1, min(50, (int) $request->input('winners_count', 1)));
        $title = trim((string) $request->input('title')) ?: ('Tirage du ' . now()->format('Y-m-d H:i'));

        $eligible = Draw::eligibleQuery($pool)->inRandomOrder()->limit($winnersCount)->get();

        if ($eligible->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun membre admissible pour ce tirage.');
        }

        $draw = Draw::create([
            'title'          => $title,
            'pool'           => $pool,
            'winners_count'  => $winnersCount,
            'note'           => trim((string) $request->input('note')) ?: null,
            'drawn_at'       => now(),
            'eligible_count' => Draw::eligibleQuery($pool)->count(),
        ]);

        foreach ($eligible->values() as $i => $member) {
            DrawWinner::create([
                'draw_id'       => $draw->id,
                'subscriber_id' => $member->id,
                'member_number' => $member->formatted_member_number ?: (string) $member->member_number,
                'name'          => trim((string) ($member->company_name ?: ($member->first_name . ' ' . $member->last_name))),
                'email'         => $member->email,
                'position'      => $i + 1,
            ]);
        }

        return redirect()->route('admin.tirage.index')
            ->with('success', 'Tirage effectué : ' . $eligible->count() . ' gagnant(s).');
    }

    public function destroy(Request $request, $id)
    {
        $draw = Draw::find($id);
        if ($draw) {
            $draw->winners()->delete();
            $draw->delete();
        }

        return redirect()->route('admin.tirage.index')->with('success', 'Tirage supprimé.');
    }
}
