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

	public static String IMAGE_FILENAME = "bild1.jpg";
	private static String NEURO_NETWORK_FILENAME = "or_perceptron.nnet";

	public static void main(String[] args) {
		double[] rgbs = RGBExtractor.marchThroughImage(IMAGE_FILENAME);

		// create new perceptron network
		NeuralNetwork neuralNetwork = new Perceptron(rgbs.length, 1);

		// create training set
		DataSet trainingSet = new DataSet(rgbs.length, 1);

		// add training data to training set (logical OR function)
		// If you want to analyse more pictures, add more DataSetRow via DataSet#addRow()
		trainingSet.addRow(new DataSetRow(rgbs, new double[] { 1 }));

		// learn the training set
		neuralNetwork.learn(trainingSet);

		// save the trained network into file
		neuralNetwork.save(NEURO_NETWORK_FILENAME);
	}

}
