<?php

namespace App\Http\Controllers\Admin;

use App\Models\Local;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use App\Models\Level;
use Validator;
use App\Http\Requests\UserRequest;

class AccountController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMember()
    {
        $members = User::where('level_id', '3')->get();

        return view('admin.account.member.index', compact('members'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addMember()
    {
        $level = Level::pluck('role', 'id');

        $local = Local::pluck('name', 'id');

        return view('admin.account.member.add', compact('level', 'local'));
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddMember(UserRequest $request)
    {
        $request->merge([
            'password' => bcrypt($request->password),
            'remove' => 0,
        ]);

        User::create($request->all());

        return redirect('admin/member/index')->with('success', trans('common.with.add_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editMember($id)
    {
        $user = User::findOrFail($id);

        $level = Level::pluck('role', 'id');

        $local = Local::pluck('name', 'id');

        return view('admin/account/member/edit', compact('level', 'user', 'local'));
    }

    /**
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postEditMember($id, UserRequest $request)
    {
        $user = User::findOrFail($id);

        $pass_old = $user->password;

        if ($request->password == $pass_old) {
            $pass_new = $pass_old;
        } else {
            $pass_new = bcrypt($request->password);
        }

        $request->merge([
            'password' => $pass_new,
            'remove' => 0,
        ]);

        $user->update($request->all());

        return redirect('admin/member/index')->with('success', trans('common.with.edit_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteMember($id)
    {
        try {
            User::remove($id);

            return redirect('admin/member/index')->with('success', trans('common.with.delete_success'));
        } catch (ModelNotFoundException $e) {
            return view('admin.404');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMulMember(Request $request)
    {
        if ($request->check == null) {
            return redirect('admin/member/index')->with('message', trans('common.with.delete_ept'));
        }

        for ($i = 0; $i < count($request->check); $i++) {
            $user = User::findOrFail($request->check[$i]);

            $user->delete();
        }

        return redirect('admin/member/index')->with('success', trans('common.with.delete_success'));
    }

    /**
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchMember(Request $req)
    {
        $members = User::member($req->key)->get();

        return view('admin.account.member.index', compact('members'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getManager()
    {
        $managers = User::where('level_id', '<>', '3')->get();

        return view('admin.account.manager.index', compact('managers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addManager()
    {
        $level = Level::pluck('role', 'id');

        $local = Local::pluck('name', 'id');

        return view('admin.account.manager.add', compact('level', 'local'));
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddManager(UserRequest $request)
    {
        $request->merge([
            'password' => bcrypt($request->password),
            'remove' => 0,
        ]);

        User::create($request->all());

        return redirect('admin/manager/index')->with('success', trans('common.with.add_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editManager($id)
    {
        $user = User::findOrFail($id);

        $level = Level::pluck('role', 'id');

        $local = Local::pluck('name', 'id');

        return view('admin/account/manager/edit', compact('level', 'user', 'local'));
    }

    /**
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postEditManager($id, UserRequest $request)
    {
        $user = User::findOrFail($id);

        $pass_old = $user->password;

        if ($request->password == $pass_old) {
            $pass_new = $pass_old;
        } else {
            $pass_new = bcrypt($request->password);
        }

        $request->merge([
            'password' => $pass_new,
            'remove' => 0,
        ]);

        $user->update($request->all());

        return redirect('admin/manager/index')->with('success', trans('common.with.edit_success'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteManager($id)
    {
        try {
            if (auth()->user()->id == $id) {
                return back()->with('message', trans('common.with.delete_error'));
            }

            User::remove($id);

            return redirect('admin/manager/index')->with('success', trans('common.with.delete_success'));
        } catch (ModelNotFoundException $e) {
            return view('admin.404');
        }
    }

    /**
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchManager(Request $req)
    {
        $managers = User::manager($req->key)->get();

        return view('admin.account.manager.index', compact('managers'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMulManager(Request $request)
    {
        if ($request->check == null) {
            return redirect('admin/manager/index')->with('message', trans('common.with.delete_ept'));
        }

        for ($i = 0; $i < count($request->check); $i++) {
            $user = User::findOrFail($request->check[$i]);

            if (auth()->user()->id = $request->check[$i]) {
                return back()->with('message', trans('common.with.delete_error'));
            }

            $user->delete();
        }

        return redirect('admin/manager/index')->with('success', trans('common.with.delete_success'));
    }
}

