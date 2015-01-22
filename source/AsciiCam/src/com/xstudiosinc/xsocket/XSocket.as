package com.xstudiosinc.xsocket {

	import adobe.utils.CustomActions;
	import flash.net.Socket;
	import flash.events.*;
	import com.xstudiosinc.xsocket.XEvent;
    import flash.system.Security;

	/**
	 * X SOCKET
	 * @author Sam Horton - X Studios Inc - 1/28/2013 6:06 PM
	 */
	
	public class XSocket extends EventDispatcher {

		private var ip:String;
		private var port:int;
		private var connectMsg:String;
		private var xSock:Socket;
		public var isConnected:Boolean = false;
		
        /**
         * NEW X SOCKET
         * @param _serverURL
         * @param _port
         * @param _connectMsg
         */
		public function XSocket(_serverURL:String, _port:int, _connectMsg:String) {
            //Security.allowDomain("*");
            //Security.allowInsecureDomain("*");

            ip = _serverURL;
            port = _port;
            connectMsg = _connectMsg;

			xSock = new Socket(ip, port);

			xSock.addEventListener(ProgressEvent.SOCKET_DATA, onSocketDataReceived);
			xSock.addEventListener(Event.CONNECT, onSocketConnected);
			xSock.addEventListener(Event.CLOSE, onSocketClosed);
			xSock.addEventListener(IOErrorEvent.IO_ERROR, onError);
			xSock.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);
			
			xSock.connect(ip, port);
		}
		
		private function onSocketConnected(e:Event):void {
            Main.Log("CONNECTED!");
			isConnected = true;
            Main.instance.OnSocketConnectSuccess();
		}
		
		private function onSocketClosed(e:Event):void {
			isConnected = false;
			Main.Log("socket closed...attempting to reconnect!");
            //xSock.connect(ip, port);
		}
		
		private function onError(e:IOErrorEvent):void {
			Main.Log(e.text);
		}
		
		private function onSecurityError(e:SecurityErrorEvent):void {
			Main.Log(e.text);
		}

        //-------------------------------------------------------------------------------

        /**
         * SEND MESSAGE
         * @param msg
         */
		public function sendMessage(msg:String):void {
			if(isConnected){
				xSock.writeUTFBytes(msg);//send string to server
				xSock.flush();
                //DebugConsole.Trace("MESSAGE SENT!");
			}
		}

        //-------------------------------------------------------------------------------
		
        /**
         * ON SOCKET DATA RECEIVED
         * @param e
         */
		private function onSocketDataReceived(e:ProgressEvent):void {
			var str:String = e.currentTarget.readUTFBytes(e.currentTarget.bytesAvailable);
			
			dispatchEvent(new XEvent(str));
		}

        //-------------------------------------------------------------------------------

        /**
         * SEND ID
         */
        public function SendID():void {
            sendMessage(connectMsg);
        }

        //-------------------------------------------------------------------------------

        /**
         * CLOSE
         */
        public function Close():void {
            try{
                xSock.close();
            } catch (e:Error) {
            }
        }
		
        //-------------------------------------------------------------------------------
		
	}

}






