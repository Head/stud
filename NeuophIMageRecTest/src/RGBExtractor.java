import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.Arrays;

import javax.imageio.ImageIO;

/**
 * Mit dieser Klasse kann man die RGB-Wete aus einem Bild extrahieren *
 *
 *
 * Geklaut von http://alvinalexander.com/blog/post/java/getting-rgb-values-for-each-pixel-in-image-using-java-bufferedi
 *
 * @author Julien, Oliver
 *
 */

public class RGBExtractor {


	public static double[] marchThroughImage(String filename) {
		BufferedImage image;
		double [] result = null;
		try {
			image = ImageIO.read(new File(filename));
			int w = image.getWidth();
			int h = image.getHeight();
			double reds[] = new double[w * h];
			double greens[] = new double[w * h];
			double blues[] = new double[w * h];

			for (int i = 0; i < h; i++) {
				for (int j = 0; j < w; j++) {
					int pixel = image.getRGB(j, i);

					double red = (pixel >> 16) & 0xff;
					double green = (pixel >> 8) & 0xff;
					double blue = (pixel) & 0xff;
					reds[i * j + j] =  red;
					greens[i * j + j] =  green;
					blues[i * j + j] =  blue;

				}
			}
			result = concatAll(reds, greens, blues);

		} catch (IOException e) {
			e.printStackTrace();
		}
		return result;
	}


	public static  double[] concatAll(double[] first, double[]... rest) {
		  int totalLength = first.length;
		  for (double[] array : rest) {
		    totalLength += array.length;
		  }
		  double[] result = Arrays.copyOf(first, totalLength);
		  int offset = first.length;
		  for (double[] array : rest) {
		    System.arraycopy(array, 0, result, offset, array.length);
		    offset += array.length;
		  }
		  return result;
		}


}
