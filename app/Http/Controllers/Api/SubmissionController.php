<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitRequest;
use App\Models\User;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function handle(Request $request)
    {

      if ($request->isMethod('get')) {
            // GET: عرض قائمة المستخدمين المسجلين
            $users = User::all(['id','name','email','created_at']);
            return response()->json([
                'status' => 'success',
                'data'   => $users
            ], 200);
        }

        if ($request->isMethod('post')) {
             try{
        /*$data = $request->validated();
        $data['password'] = Hash::make('123456');

        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'data'   => $user
        ], 201);*/

         $validated = app(SubmitRequest::class)->validated(); // تحقق Validation
                $validated['password'] = Hash::make('123456');
                $user = User::create($validated);

                return response()->json([
                    'status' => 'success',
                    'data'   => $user
                ], 201);


        }catch(\Exception $e){

              ErrorLog::create([
                'endpoint'      => '/api/submit',
                'request_data'  => json_encode($request->all()),
                'error_message' => $e->getMessage()
            ]);

            // سجّل أيضاً في Laravel log
            Log::error('Submission Error: '.$e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ في السيرفر. سيتم التحقيق فيه.'
            ], 500);


         }

      }

      // أي Method أخرى
        return response()->json([
            'status' => 'error',
            'message' => 'Method not allowed'
        ], 405);
    }


    // حذف مستخدم واحد
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status'=>'error', 'message'=>'User not found'], 404);
        }

        $user->delete();
        return response()->json(['status'=>'success', 'message'=>'User deleted'], 200);
    }

    // حذف مجموعة أو كل المستخدمين
    public function destroyAll(Request $request)
    {
        $ids = $request->input('ids'); // توقع مصفوفة ids من body JSON
        if ($ids && is_array($ids)) {
            User::whereIn('id', $ids)->delete();
            return response()->json(['status'=>'success', 'message'=>'Selected users deleted'], 200);
        }

        // إذا لم يتم إرسال ids، حذف الكل
        User::query()->delete();
        return response()->json(['status'=>'success', 'message'=>'All users deleted'], 200);
    }
}
