<?php

class AdminMovieController extends AdminController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    protected $movie;

    public function __construct(Movie $movie)
    {
        parent::__construct();
        $this->movie = $movie;
    }

    public function getIndex()
    {
        // Title
        $title = "Movies";
        // Grab all the users
        $movies = $this->movie;
        // Show the page
        return View::make('admin/movie/index', compact('movies', 'title'));
    }
    public function getData()
    {
        $movies = Movie::select(array('movie.id', 'movie.name','movie.image','movie.review','movie.is_active', 'movie.created_at'))->orderBy('movie.id','asc');//,'movie.review'

        return Datatables::of($movies)
         ->edit_column('name','@if($image != "")
                        <img src="{{{\'../img/\'.$image}}}" width="50" /> {{ $name }}
                        @else
                            <img src="{{{\'../img/defalut-avatar.png\'}}}" width="50" /> {{$name}}
                        @endif
                        ')
         
         ->edit_column('review','{{{ mb_substr($review,0,250)."..." }}}')

         ->edit_column('is_active','@if($is_active == "1")
                            {{ "Yes" }}
                        @else
                            {{ "No" }}
                        @endif')

         ->edit_column('created_at','{{{ date("Y-m-d",strtotime($created_at)) }}}')
      

        ->add_column('actions', '<a href="{{{ URL::to(\'admin/movie/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-default">{{{ Lang::get(\'button.edit\') }}}</a>
                                
                                    <a href="{{{ URL::to(\'admin/movie/\' . $id . \'/delete\' ) }}}" class="btn btn-xs btn-danger">{{{ Lang::get(\'button.delete\') }}}</a>
                              
            ')

        ->remove_column('id')
        ->remove_column('image')
        ->make();
    }

    public function getCreate()
    {
        $title = "Add Movie";

        // Mode
        $mode = 'create';

        // Show the page
        return View::make('admin/movie/add', compact('title'));
    }
    
    public function postCreate()
    {

        $rules = array(
            'name' => 'required',            
            'review' => 'required|min:250',
            'is_active' => 'required',
        );
        $messages = array(
            'required' => 'Required',
        );
       
        $validator = Validator::make(Input::all(), $rules,$messages);
        // Check if the form validates with success
        if ($validator->passes())
        {
            $inputs = Input::except('csrf_token');
            $this->movie->name = $inputs['name'];
            $this->movie->review = $inputs['review'];
            $this->movie->is_active = $inputs['is_active'];
            $this->movie->user_id = $inputs['user_id'];
            $this->movie->save();
            if ($this->movie->id)
            {
                 if (Input::hasFile('image')) {
                    $file            = Input::file('image');
                    $destinationPath = public_path().'/img/';
                    $filename        = $this->movie->randomPrefix() . '.' . $file->getClientOriginalExtension();
                    $uploadSuccess   = $file->move($destinationPath, $filename);
                    $this->movie->resizeImage($destinationPath.$filename);
                    $this->movie->image =$filename;
                    $this->movie->save();
                }
            }
            return Redirect::to('admin/movie/' . $this->movie->id . '/edit')->with('success',"Movie added");
        }
        else
        {
          
            return Redirect::to('admin/movie/create')
                ->withErrors($validator);
        }
    }

    public function getEdit($movie)
    {

        $title = "Update Movie";
        return View::make('admin/movie/edit', compact('movie', 'title'));
    }
    public function postEdit($movie)
    {
        $rules = array(
            'name' => 'required',            
            'review' => 'required|min:250',
            'is_active' => 'required',
        );
        $messages = array(
            'required' => 'Required',
        );
       
        $validator = Validator::make(Input::all(), $rules,$messages);
        // Check if the form validates with success
        if ($validator->passes())
        {
            $inputs = Input::except('csrf_token');
            $movie->name = $inputs['name'];
            $movie->review = $inputs['review'];
            $movie->is_active = $inputs['is_active'];
            $movie->save();
            if ($movie->id)
            {
                 if (Input::hasFile('image')) {
                    $file            = Input::file('image');
                    $destinationPath = public_path().'/img/';
                    $filename        = $movie->randomPrefix() . '.' . $file->getClientOriginalExtension();
                    $uploadSuccess   = $file->move($destinationPath, $filename);
                    $movie->resizeImage($destinationPath.$filename);
                    $movie->image =$filename;
                    $movie->save();
                }
            }
            return Redirect::to('admin/movie/' . $movie->id . '/edit')->with('success',"Movie updated");
        }
        else
        {
          
            return Redirect::to('admin/movie/create')
                ->withErrors($validator);
        }
    }
    public function getDelete($movie)
    {
        $moviename = $movie->name;
        if($movie->delete()) {
            // Redirect to the role management page
            return Redirect::to('admin/movie')->with('success', $moviename." Movie deleted");
        }

        // There was a problem deleting the role
        return Redirect::to('admin/movie')->with('error',"Movie could not be deleted");
    }

	public function index()
	{
        $movie = DB::table('movie')
            ->orderBy('added_at')
            ->take(30)
            ->get();
        return View::make('admin.movie.index')
            ->with('movie', $movie);
	}

    public function stock($isin)
	{
        $stock = DB::table('stock')->where('isin',$isin)->get();
        return View::make('stock.stock')
            ->with('stock', $stock);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('admin.movie.add');
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $destinationPath = '';
        $filename        = '';

        if (Input::hasFile('image')) {
            $file            = Input::file('image');
            $destinationPath = public_path().'/img/';
            $filename        = str_random(6) . '_' . $file->getClientOriginalName();
            $uploadSuccess   = $file->move($destinationPath, $filename);
        }

        $movie = (['name'       => Input::get('name'),
            'review'       => Input::get('review'),
            'img'        => $filename]);

            DB::table('movie')->insert($movie);
            return Redirect::to('/');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $movie = DB::table('movie')->where('id', $id)->first();
        return View::make('admin.movie.view')->with('movie', $movie);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $movie = DB::table('movie')->where('id', $id)->first();
        return View::make('admin.movie.edit')->with('movie', $movie);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function wordAnalysis($id)
	{
        $movie = DB::table('movie')->where('id', $id)->first();
        $movie->reviewc = count($this->mb_str_word_count($movie->name,2));
        print_r($movie->reviewc);
        return View::make('admin.movie.word')->with('movie', $movie);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{

        DB::table('movie')
            ->where('id', $id)
            ->update(array('name' => Input::get('name'),'review' => Input::get('review')));
        Session::flash('msg', "Update Sucessfully");
        return Redirect::to('/edit/'.$id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    function mb_str_word_count($string, $format = 0, $charlist = '[]') {
        mb_internal_encoding( 'UTF-8');
        mb_regex_encoding( 'UTF-8');

        $words = mb_split('[^\x{0600}-\x{06FF}]', $string);
        switch ($format) {
            case 0:
                return count($words);
                break;
            case 1:
            case 2:
                return $words;
                break;
            default:
                return $words;
                break;
        }
    }

    function mb_str_para_count($string, $format = 0, $charlist = '[]') {
        mb_internal_encoding( 'UTF-8');
        mb_regex_encoding( 'UTF-8');

        $words = mb_split('[^\x{0600}-\x{06FF}]', $string);
        switch ($format) {
            case 0:
                return count($words);
                break;
            case 1:
            case 2:
                return $words;
                break;
            default:
                return $words;
                break;
        }
    }

    function mb_str_sent_count($string, $format = 0, $charlist = '[]') {
        mb_internal_encoding( 'UTF-8');
        mb_regex_encoding( 'UTF-8');

        $words = mb_split('[^\x{0600}-\x{06FF}]', $string);
        switch ($format) {
            case 0:
                return count($words);
                break;
            case 1:
            case 2:
                return $words;
                break;
            default:
                return $words;
                break;
        }
    }

}