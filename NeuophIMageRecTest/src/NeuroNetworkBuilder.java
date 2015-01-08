import java.io.File;
import java.util.Date;

import org.neuroph.core.NeuralNetwork;
import org.neuroph.core.data.DataSet;
import org.neuroph.core.data.DataSetRow;
import org.neuroph.nnet.Perceptron;


/**
 *
 * Diese Klasse erstellt das Neuronale Netzwerk zur Bildererkennung
 *
 * Das generierte neuro-Netz wird dann unter {@value #NEURO_NETWORK_FILENAME} abgespeichert.
 *
 *
 * Quelle:  "./neuroph-2.9/doc/Getting Started with Neuroph 2.9.pdf" Seite 12 aus dem neuroph-Framework zip-Datei(http://sourceforge.net/projects/neuroph/files/neuroph-2.9/neuroph-2.9.zip/download)
 *
 * @author Julien, Oliver
 *
 */

public class NeuroNetworkBuilder {

	private static final double CATEGORY_LANDSCAPE = 0;
	private static final double CATEGORY_PORTRAIT = 1;
	private static final double CATEGORY_STILLLIFE = 2;
	private static final double CATEGORY_REALLIFE = 3;
	private static final double CATEGORY_RELIGIOUS = 4;
	private static final double CATEGORY_HISTORY = 5;
	private static final double CATEGORY_ABSTRACT = 6;
	public static String IMAGE_FILENAME = "bild1.jpg";
	private static String NEURO_NETWORK_FILENAME = "or_perceptron.nnet";

	public static void main(String[] args) {


		initNeuroLearn();
	}

	private static void initNeuroLearn() {
		String path = "/var/www/stud/AAI/imgs/1_landscape/resized";
		File images [] = new File(path).listFiles();
		double[] rgbs = RGBExtractor.marchThroughImage(images[0].getAbsolutePath());

		Date date = new Date();
		System.out.println("Start :" + date.toString());
		// create new perceptron network
		NeuralNetwork neuralNetwork = new Perceptron(rgbs.length, 1);

		date = new Date();
		System.out.println("NeuroN created at: " + date.toString());

		// create training set

		//ca 3 Minuten bis hierher
		path = "/var/www/stud/AAI/imgs/1_landscape/resized";
		images = new File(path).listFiles();
		DataSet trainingsData = addTrainingsData(CATEGORY_LANDSCAPE, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 0 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/2_portrait/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_PORTRAIT, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 1 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/3_still-life/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_STILLLIFE, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 2 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/4_real-life/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_REALLIFE, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 3 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/5_religious/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_RELIGIOUS, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 4 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/6_history/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_HISTORY, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 5 done at: " + date.toString());

		path = "/var/www/stud/AAI/imgs/7_abstract/resized";
		images = new File(path).listFiles();
		trainingsData = addTrainingsData(CATEGORY_ABSTRACT, neuralNetwork, images);
		neuralNetwork.learn(trainingsData);

		date = new Date();
		System.out.println("Category 6 done at: " + date.toString());

		// save the trained network into file
		neuralNetwork.save(NEURO_NETWORK_FILENAME);
	}

	private static DataSet addTrainingsData(double category, NeuralNetwork neuralNetwork, File images []) {
		double[] rgbs;
		// add training data to training set (logical OR function)
		// If you want to analyse more pictures, add more DataSetRow via DataSet#addRow()
		//Wir wollen nur die erste hälfte zum trainieren benutzen
		//und die 2. zum testen

		int halfCountImages = (images.length / 2);
		System.out.println("Amount of images to learn:" + halfCountImages);

		rgbs = RGBExtractor.marchThroughImage(images[0].getAbsolutePath());
		DataSet trainingSet = new DataSet(rgbs.length, 1);

		for (int i = 0; i < halfCountImages; i++){
			trainingSet.addRow(new DataSetRow(rgbs, new double[] {category}));
			System.out.println(String.format("Adding %s", images[i].getName()));
		}

		return trainingSet;
	}

}
