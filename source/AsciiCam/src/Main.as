package {
	import flash.display.*;
    import flash.events.Event;
    import flash.events.*;
    import flash.media.*;
    import flash.filesystem.*;
    import com.xstudiosinc.xsocket.*;
    import flash.text.*;
    import flash.utils.Timer;
    import flash.ui.Keyboard;
    import flash.desktop.NativeApplication;
    import flash.filesystem.File;

	
	/**
	 * ASCII CAM
	 * @author Sam Horton - X Studios Inc - 3/13/2013 11:02 AM
	 * @link http://xstudiosinc.com
	 */
	public class Main extends MovieClip {

        public static var instance:Main;

		public var xsock:XSocket;
        private var mCam:Camera;
        private var mVid:Video;
        private var mCamBD:BitmapData;
        private var mcContainer:MovieClip;
        private var mWidth:int = 300;
        private var mHeight:int = 85;
        private var mFrameRate:uint = 30;
        private var mFontSize:Number = 10;
        private var mTimer:Timer;
        private var mComDelay:int = 60;//msecs between socket calls


        private var mOutputTextField:TextField;

        //-------------------------------------------------------------------------------

        /**
         * MAIN
         */
		public function Main() {

            instance = this;

            _InitApp()
			
            stage.frameRate = mFrameRate;

		}

        //-------------------------------------------------------------------------------

        /**
         * INIT APP
         */
        private function _InitApp():void {

            //xsock = new XSocket("192.168.1.173", 1337, "");
            //xsock = new XSocket("127.0.0.1", 1337, "");
            //xsock.addEventListener(XEvent.MSG_READY, _OnSocketMSGReceived);

            mTimer = new Timer(mComDelay);
            mTimer.addEventListener(TimerEvent.TIMER, _OnComUpdate);

            if (xsock == null) {
                _InitializeView();
            }

        }

        //-------------------------------------------------------------------------------

        /**
         * ON SOCKET MESSAGE RECEIVED
         * @param e
         */
        private function _OnSocketMSGReceived(e:XEvent):void {
            Log(e.serverString);
            try {

                //{"rows":"65","cols":"251","time":65}
                var obj:Object = JSON.parse(e.serverString);
                mComDelay = obj.time + 10;
                mWidth    = obj.cols;
                mHeight   = obj.rows;

                _InitializeView();

            } catch (e:Error) {

                Log(e.message);

            }
        }

        //-------------------------------------------------------------------------------

        /**
         * ON COM UPDATE
         * @param e
         */
        private function _OnComUpdate(e:TimerEvent=null):void {
            if (xsock!=null && xsock.isConnected) {
                xsock.sendMessage(Image2Ascii.latestAsciiString);
            }
            mOutputTextField.text = Image2Ascii.latestAsciiString;
        }

        //-------------------------------------------------------------------------------

        /**
         * ON SOCKET CONNECT SUCCESS();
         */
        public function OnSocketConnectSuccess():void {

        }

        //-------------------------------------------------------------------------------

        /**
         * INIT CAM
         */
        private function _InitCam():void {
            Image2Ascii.Init();

            if(mCam==null) mCam = Camera.getCamera();

            mCam.setMode(mWidth, mHeight, mFrameRate, false);
            mCam.setQuality(0, 100);
            mCam.setKeyFrameInterval(30);

            if (mVid == null) {
                mVid = new Video(mWidth, mHeight);
                mcContainer = new MovieClip();
                mcContainer.addChild(mVid);
                mVid.attachCamera(mCam);
                this.addChild(mcContainer);
                mCamBD = new BitmapData(mWidth, mHeight, false, 0x000000);
            }else {
                mcContainer.width = mWidth;
                mcContainer.width = mWidth;
            }

            mcContainer.visible = false;

        }

        //-------------------------------------------------------------------------------

        /**
         * INIT TEXT FIELD
         */
        private function _InitTextField():void {
            if(mOutputTextField==null){
                mOutputTextField = new TextField;
                var tf:TextFormat = new TextFormat("Courier New");
                tf.size = 7;
                mOutputTextField.defaultTextFormat = tf;
                mOutputTextField.text = ".";
                var tw:Number = mOutputTextField.textWidth;
                var th:Number = mOutputTextField.textHeight;
                mOutputTextField.text = "";

                //aspect formula = original height / original width x new width = new height
                //mWidth = .5 * mWidth;
                //mHeight = (stage.fullScreenHeight * (tw / th))*downsample;


                //tf.leading = -2;
                //tf.kerning = int(mFontSize / 2);


                this.addChild(mOutputTextField);
            }

            mOutputTextField.width  = mWidth;
            mOutputTextField.height = mHeight;
            //mOutputTextField.x = 400;
            mOutputTextField.textColor = 0x009900;
            mOutputTextField.wordWrap = false;
            //mOutputTextField.selectable = false;

        }


        //-------------------------------------------------------------------------------

        /**
         * UPDATE
         * @param e
         */
        private function _Update(e:Event):void {
            _GetCamImage();
        }

        //---------------------------------------------------------------

        /**
         * GET CAM IMAGE
         */
        private function _GetCamImage():void {
            if (mCam != null) {
                mCamBD.draw(mVid);
                Image2Ascii.Convert(mCamBD);
            }
        }

        //-------------------------------------------------------------------------------

        /**
         * LOG
         * @param _msg
         */
        public static function Log(_msg:String):void {
            trace(_msg);
        }

        //---------------------------------------------------------------

         /**
         * ON KEY DOWN
         * @param e
         */
        private function _OnKeyDown(e:KeyboardEvent):void  {
            if (e.keyCode == Keyboard.ESCAPE) {
                TerminateApp();
            }
        }

        //-------------------------------------------------------------------------------

        /**
         * TERMINATE APP (OFFLINE ONLY)
         */
        public function TerminateApp():void {
            NativeApplication.nativeApplication.exit();
        }

        //-------------------------------------------------------------------------------

        /**
         * GET PATH
         * @param string
         */
        public static function GetPath(path:String):String {
            var f:File = File.applicationDirectory.resolvePath(path);
            return f.nativePath;
        }

        //-------------------------------------------------------------------------------

        /**
         * INIT VIEW
         */
        private function _InitializeView():void {
            var t:Timer;
            t = new Timer(1000, 1);
            t.addEventListener(TimerEvent.TIMER,_InitializeViewForReal);
            t.start();
        }

        //-------------------------------------------------------------------------------

        /**
         * INIT VIEW FOR REAL
         * @param e
         */
        private function _InitializeViewForReal(e:TimerEvent=null):void {
            stage.displayState = StageDisplayState.FULL_SCREEN_INTERACTIVE;
            stage.scaleMode = StageScaleMode.EXACT_FIT;
            stage.align = StageAlign.TOP_LEFT;
            stage.addEventListener(Event.RESIZE, _OnStageResize);

            _InitTextField();
            _InitCam();
            mTimer.start();
            _OnStageResize();

            this.addEventListener(Event.ENTER_FRAME, _Update);
        }

        //---------------------------------------------------------------

        /**
         * ON STAGE RESIZE
         * @param e
         */
        private function _OnStageResize(e:Event=null):void {
            trace("Resize", stage.stageWidth, stage.stageHeight, stage.fullScreenWidth, stage.fullScreenHeight);
            mcContainer.width = stage.stageWidth;
            mcContainer.height = stage.stageHeight;

            mOutputTextField.width = stage.stageWidth;
            mOutputTextField.height = stage.stageHeight;

            Image2Ascii.sampleXStep = Math.ceil(mWidth / stage.fullScreenWidth);
            Image2Ascii.sampleYStep = Math.ceil(mHeight / stage.fullScreenHeight);

        }

		
	}
	
}