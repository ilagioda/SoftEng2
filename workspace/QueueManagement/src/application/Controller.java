package application;

/**
 * Sample Skeleton for 'Screen.fxml' Controller Class
 */

import java.net.URL;
import java.util.ResourceBundle;

import javafx.collections.ListChangeListener;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import model.Counter;
import model.Model;
import model.Ticket;

public class Controller {

	Model model;

	@FXML // ResourceBundle that was given to the FXMLLoader
	private ResourceBundle resources;

	@FXML // URL location of the FXML file that was given to the FXMLLoader
	private URL location;

	@FXML // fx:id="ticketC1"
	private TextField ticketC1; // Value injected by FXMLLoader

	@FXML // fx:id="ticketC2"
	private TextField ticketC2; // Value injected by FXMLLoader

	@FXML // fx:id="ticketC3"
	private TextField ticketC3; // Value injected by FXMLLoader

	@FXML // fx:id="queueA"
	private TextArea queueA; // Value injected by FXMLLoader

	@FXML // fx:id="queueB"
	private TextArea queueB; // Value injected by FXMLLoader

	@FXML // This method is called by the FXMLLoader when initialization is complete
	void initialize() {
		assert ticketC1 != null : "fx:id=\"ticketC1\" was not injected: check your FXML file 'Screen.fxml'.";
		assert ticketC2 != null : "fx:id=\"ticketC2\" was not injected: check your FXML file 'Screen.fxml'.";
		assert ticketC3 != null : "fx:id=\"ticketC3\" was not injected: check your FXML file 'Screen.fxml'.";
		assert queueA != null : "fx:id=\"queueA\" was not injected: check your FXML file 'Screen.fxml'.";
		assert queueB != null : "fx:id=\"queueB\" was not injected: check your FXML file 'Screen.fxml'.";
	}

	public void setModel(Model model) {
		this.model = model;

		model.getList1().addListener(new ListChangeListener<Ticket>() {

			@Override
			public void onChanged(ListChangeListener.Change change) {

				ObservableList<Ticket> lista1 = model.getList1();
				if (!lista1.isEmpty())
					queueA.setText(lista1.toString());
				else
					queueA.setText(" ");
			}
		});

		model.getList2().addListener(new ListChangeListener() {

			@Override
			public void onChanged(ListChangeListener.Change change) {

				ObservableList<Ticket> lista2 = model.getList2();
				if (!lista2.isEmpty())
					queueB.setText(lista2.toString());
				else
					queueB.setText(" ");
			}
		});

	}

	public void setTextCounter(int counterID, Ticket t) {
		switch (counterID) {
		case 0:
			ticketC1.setText(t.toString());
			break;
		case 1:
			ticketC2.setText(t.toString());
			break;
		case 2:
			ticketC3.setText(t.toString());
			break;
		}

	}

}
