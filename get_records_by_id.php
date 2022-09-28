public function index(Request $request)
    {
        $token = $request->input('api_token');

        $user = DB::table('users')->select('id')->where('api_token', '=', hash('sha256', $token))->get();
        $user_id = get_object_vars($user[0])['id'];

        $news = DB::table('news')->where('user_id', '=', $user_id)->get();

        return $news;
    }
