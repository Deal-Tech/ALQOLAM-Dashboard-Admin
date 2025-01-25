<?php

namespace App\Http\Controllers;

use App\Models\Tahun;
use Illuminate\Http\Request;

class TahunController extends Controller
{
    public function index()
    {
        return Tahun::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:255',
        ]);

        $tahun = Tahun::create($request->all());

        return response()->json($tahun, 201);
    }

    public function show($id)
    {
        $tahun = Tahun::find($id);

        if (is_null($tahun)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($tahun);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|string|max:255',
        ]);

        $tahun = Tahun::find($id);

        if (is_null($tahun)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $tahun->update($request->all());

        return response()->json($tahun);
    }

    public function destroy($id)
    {
        $tahun = Tahun::find($id);

        if (is_null($tahun)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $tahun->delete();

        return response()->json(null, 204);
    }
}