<?php

namespace KUHdo\Webhookable\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use KUHdo\Webhookable\Requests\WebHookRequest;
use KUHdo\Webhookable\WebHook;

class RestHooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->webHooks()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WebHookRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebHookRequest $request)
    {
        if(auth()->user()->can('create', Webhook::make())) {
            $input = $request->all();
            $webHook = auth()->user()->webHooks()->create([
                "url" => $input["url"],
                "event" => $input["event"]
            ]);
            return response()->json($webHook);
        } else {
            return response()->json(false, 403);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Int $id)
    {
        $webHook = WebHook::findOrFail($id);
        if(auth()->user()->can('view', $webHook)) {
            return response()->json($webHook);
        }
        return response()->json(false, 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WebHookRequest $request
     * @param Int $id
     * @return \Illuminate\Http\Response
     */
    public function update(WebHookRequest $request, Int $id)
    {
        $webHook = WebHook::findOrFail($id);
        if(auth()->user()->can('update', $webHook)) {
            $webHook->update($request->all());
            return response()->json($webHook);
        } else {
            return response()->json(false, 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Int $id)
    {
        $webHook = WebHook::findOrFail($id);
        if(auth()->user()->can('delete', $webHook)) {
            $webHook->delete();
            return response()->json(true, 200);
        }
        return response()->json(false, 403);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function pollForTrigger () {
        return response()->json(
            WebHook::getPossibleEventsAttribute()
        );
    }
}
