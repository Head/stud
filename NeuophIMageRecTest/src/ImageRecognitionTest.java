
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.HashMap;

import javax.imageio.ImageIO;

import org.neuroph.core.NeuralNetwork;
import org.neuroph.imgrec.ImageRecognitionPlugin;
import org.neuroph.imgrec.image.Dimension;

/**
 *
 *	Diese Klasse testet das neuronale Netzwerk mit dem Bild was vorher zum Trainieren genutzt wurde
 *
 * Quelle: http://neuroph.sourceforge.net/image_recognition.html -> "3. Using Neuroph Image Recognition in Your Applications"
 *
 * @author Julien, Oliver
 *
 */

public class ImageRecognitionTest {

	//TODO:
	// Check if all images have this dimension
	private static Dimension IMAGE_DIMENSION = new Dimension(300, 146);

	public static void main(String[] args) {
		// load trained neural network
		NeuralNetwork nnet = NeuralNetwork.load("or_perceptron.nnet");

		// get the image recognition plugin from neural network
		ImageRecognitionPlugin imageRecognition = new ImageRecognitionPlugin(IMAGE_DIMENSION);

		nnet.addPlugin(imageRecognition);

		// image recognition is done here
		try {
			String path = "/var/www/stud/AAI/imgs/2_portrait/resized";
			File images [] = new File(path).listFiles();
			for (int i = (images.length / 2); i < images.length; i++){

				//read image
				BufferedImage image = ImageIO.read( new File( images[i].getAbsolutePath() ) );

				//start recognition
				HashMap<String, Double> output = imageRecognition.recognizeImage(image);

				//show result
				System.out.println(output.toString());
			}
		} catch (IOException ioe) {
			ioe.printStackTrace();
		}
	}
}
