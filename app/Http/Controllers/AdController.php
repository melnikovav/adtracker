<?php

namespace App\Http\Controllers;

use App\Model\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    /**
     * Show best match ad.
     *
     * @return Response
     */
    public function showOne()
    {
        $ad = Ad::whereColumn('amount', '>', 'views')
            ->orderBy('price', 'desc')
            ->first();

        if (!$ad) {
            return response()->json(['err' => 'Нет подходящих объявлений']);
        }

        $ad->views = $ad->views + 1;
        $ad->save();

        return response()->json($ad);
    }

    /**
     * Create a new ad.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validatedData = $this->validate($request, [
            'text' => 'required',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if (!$request->hasFile('banner')) {
            return response()->json(['err' => 'Не загружен файл']);
        }

        try {
            $file = $request->file('banner');

            $bannerPath = config('app.bannerPath');
            $fileName = uniqid() . '.' . $file->clientExtension();

            $file->move($bannerPath, $fileName);
        } catch (\Exception $e) {
            return response()->json(['err' => 'Не удалось сохранить файл']);
        }

        $validatedData['banner'] = $fileName;

        $ad = Ad::create($validatedData);

        return response()->json($ad, 201);
    }

    /**
     * Update ad.
     *
     * @param  int      $id
     * @param  Request  $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $validatedData = $this->validate($request, [
            'text' => 'required',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if (!$request->hasFile('banner')) {
            return response()->json(['err' => 'Не загружен файл']);
        }

        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['err' => 'Не найдено объявление']);
        }

        try {
            $file = $request->file('banner');

            $bannerPath = config('app.bannerPath');
            $fileName = uniqid() . '.' . $file->clientExtension();

            $file->move($bannerPath, $fileName);

            @unlink($bannerPath . $ad->banner);
        } catch (\Exception $e) {
            return response()->json(['err' => 'Не удалось сохранить файл']);
        }

        $validatedData['banner'] = $fileName;

        $ad->update($validatedData);

        return response()->json($ad, 200);
    }
}
