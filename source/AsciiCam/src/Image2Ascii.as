package  {
    import flash.display.BitmapData;
    import flash.display.Shader;
    import flash.filters.*;
    import flash.geom.*;

	/**
     * IMAGE TO ASCII
     * @author Sam Horton - X Studios Inc - 3/13/2013 1:23 PM
	 * @link http://xstudiosinc.com
     */
    public class Image2Ascii {

        public static var sampleXStep:int = 1;
        public static var sampleYStep:int = 1;

        public static var brightnessChars:Array;
        public static var whiteMax:uint;
        public static var latestAsciiString:String="...";
		private static var lastWhite:uint;
        private static var pnt:Point;
        private static var range:int;
        private static var ct:ColorTransform;
        private static var blur:BlurFilter;


        public static function Init():void {

            //brightnessChars = [" ", ".", ",", "~", "*", "^", "o", "0"];
            brightnessChars = [" ", " ", " ", ".", ",", "-", "+", "P", "?", "$", "@", ,"@","@", "#"];
            //brightnessChars = [' ','*','*','╒','╜','╔','╩','╢','╫','♣','■','♣','■','▒','▒','▒','▒','▓','▓','▓','▓','▓','▓','▓','█','█'];
            //brightnessChars = ['¨', '¨', '·', '¸', '¡', '÷', '°', '¬', 'ª', '¢', 'î', '&', 'ò', 'ô', 'Ô', 'À', 'Õ', '@', 'Å', '§', 'å', '®', '®', 'Ñ', 'Æ'];
			//brightnessChars.reverse();
            whiteMax = 10 * 3;
			lastWhite = 0;
            pnt = new Point();
            range = brightnessChars.length;
            ct = new ColorTransform();
            blur = new BlurFilter(1, 1, 1);


        }

        /**
         * CONVERT IMAGE TO ASCII STRING
         * @param _bd
         */
        public static function Convert(_bd:BitmapData):void {
            _bd.applyFilter(_bd, _bd.rect, pnt, blur);

            _bd.threshold(_bd, _bd.rect, pnt, "<",0x020202ff, 0, 0xffffffff, false);

            var x:uint = 0;
            var y:uint = 0;
            var w:uint = _bd.width;
            var h:uint = _bd.height;
            var colorValue:uint;
            var brightnessIndex:uint = 0;
            var char:String;

            latestAsciiString = "";
			
            //e.getPixel(i,0).toString(16);
			//luminance =  0.3086 * red + 0.6094 * green + 0.0820 * blue;
            for (y = 0; y < h; y += sampleYStep) {
                for (x = 0; x < w; x += sampleXStep) {
                    ct.color = _bd.getPixel(x, y);
                    brightnessIndex = (ct.redOffset + ct.greenOffset + ct.blueOffset);
					if (brightnessIndex > lastWhite) lastWhite = brightnessIndex;
					
					brightnessIndex = (brightnessIndex/whiteMax) * range;
                    char = brightnessChars[brightnessIndex];
					if (char == null) char = "#";
                    latestAsciiString += char;
                }
                latestAsciiString += "\n";
            }
			
			
            whiteMax = lastWhite;
			
        }

    }

}