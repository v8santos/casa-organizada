<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function index(Request $request)
    {
        $households = Household::whereHas(
            'users',
            fn ($query) => $query->where('user_id', $request->user()->id)
        )->get();

        if (! $households) {
            return redirect()->route('households.create');
        }

        return view('household.index', compact('households'));
    }

    public function setHousehold(Request $request)
    {
        $household = Household::findOrFail($request->household_id);

        cookie()->queue(
            "household_id",
            $household->id,
            60 * 24
        );

        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $household = Household::create(['name' => $request->name]);

        $household->users()->attach($request->user()->id);

        return redirect()->route('home');
    }
}
