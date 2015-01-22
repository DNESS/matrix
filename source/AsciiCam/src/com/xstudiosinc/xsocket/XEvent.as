package com.xstudiosinc.xsocket {
	import flash.events.EventDispatcher;
	import flash.events.Event;
	/**
	 * ...
	 * @author Sam Horton - X Studios Inc - 4/1/2010 1:12 PM
	 */
	
	public class XEvent extends Event {
		public static const MSG_READY:String = "messageReady";
		public var serverString:String;	//holds the server message we want to broadcast to whoever is listening
		
		public function XEvent(istr:String) {
			super(MSG_READY);			//since we extend event, this fires off the normal event processes
			serverString = istr;		//this var allows our custom event to have data associated with it
		}
		
	}

}