<?php
$Styles = array();
$Widgets__list = array();
#
$YC__WidgetsMachine = new YC__WidgetsMachine;
# INTRO SETUP
	$ShowIntro = false;
	$HomeIntro = ( is_array( get_option('HomeIntro') ) ) ? get_option('HomeIntro') : array();
	$HomeIntro = ( is_array( $HomeIntro ) ) ? $HomeIntro : array();
	#
	if( isset( $HomeIntro['SelectedModel'] ) && !empty( $HomeIntro['SelectedModel'] ) ){ $ShowIntro = true;
		$ActiveModel = $HomeIntro['SelectedModel'];

		$Intro_Data = ( ( isset( $HomeIntro[ $ActiveModel ] ) ) ) ? $HomeIntro[ $ActiveModel ] : array();
		#
		if( isset( $Intro_Data[ 'TextAreaColor' ] ) && strpos($Intro_Data[ 'TextAreaColor' ], PHP_EOL ) !== FALSE ){
			$Intro_Data[ 'TextAreaColor' ] = explode(PHP_EOL, $Intro_Data[ 'TextAreaColor' ]);
		}else{
			$Intro_Data[ 'TextAreaColor' ] = array();	
		}
		$HeaderAttr = 'style="'.implode('', $Intro_Data[ 'TextAreaColor' ]).'"';
		$Intro_Data['AttrStyle'] = $HeaderAttr;
		#
		$Path_style__Intro = $this->StylesPath.'YourColor__Intro';
		$StyleFields__Intro = array();
		#
		foreach( glob($Path_style__Intro.'/*.css') as $cs__file ) {
			$current_file_name = explode('YourColor__Intro/', $cs__file)[1];
			$current_file_name = explode('/', $current_file_name)[0];
			$current_file_name = explode('.css', $current_file_name)[0];
			$StyleFields__Intro[] = $current_file_name;
		}

		# Style FILES.
		if( in_array( $ActiveModel ,$StyleFields__Intro ) ){
			$Styles[$ActiveModel] = 'YourColor__Intro/'.$ActiveModel.'.css';
		}
	}

# HOME WIDGETS SETUP 
	$home_widgets = ( is_array( get_option( 'widgets_home__meta' ) ) ) ? get_option( 'widgets_home__meta' ) : array();
	$home_widgets = ( is_array( $home_widgets ) ) ? $home_widgets : array();

	if( !empty( $home_widgets ) ){
		$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues($home_widgets);
		$Styles = array_merge($Styles,$widgets__Enqueues);

		$extraxt__Widgetlist = $YC__WidgetsMachine->get__widgets__list($home_widgets);
		$Widgets__list = array_merge($Widgets__list,$extraxt__Widgetlist);
	}				


$this->Part('header',array('Styles'=>$Styles,'IntroPage'=>true,'Widgets__list'=>$Widgets__list));

	# INTRO UI.
	if( $ShowIntro == true ) $YC__WidgetsMachine->widgets__model__UI( array( 'model__value'=> $HomeIntro) );

	# WIDGETS UI
	if( !empty( $home_widgets ) )  $YC__WidgetsMachine->widgets___UI( array('Widgets_data'=>$home_widgets,'WidgetID'=>'home_widgets') );

$this->Part('footer',array('Styles'=>$Styles,'Widgets__list'=>$Widgets__list));