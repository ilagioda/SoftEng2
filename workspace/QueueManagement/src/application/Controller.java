package application;

/**
 * Sample Skeleton for 'Screen.fxml' Controller Class
 */

import java.net.URL;
import java.util.ArrayList;
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

		// Listener : whenever the list of tickets related to service 1 changes the
		// method onChanged is called.
		model.getList1().addListener(new ListChangeListener<Ticket>() {

			// This method updates the view by printing the new queue and the tickets served
			// to the new counters.
			@Override
			public void onChanged(ListChangeListener.Change change) {

				ObservableList<Ticket> lista1 = model.getList1();
				
				queueA.clear();
				
				if (!lista1.isEmpty()) {
					for (Ticket t : lista1) {
						if(t!=null)
							queueA.appendText(t.toString() + System.lineSeparator());
					}
				}
				
				updateCounters();
			}
		});

		// Listener : whenever the list of tickets related to service 2 changes the
		// method onChanged is called.
		model.getList2().addListener(new ListChangeListener<Ticket>() {

			// This method updates the view by printing the new queue and the tickets served
			// to the new counters.
			@Override
			public void onChanged(ListChangeListener.Change change) {

				ObservableList<Ticket> lista2 = model.getList2();
				queueB.clear();
				
				if (!lista2.isEmpty()) {
					for (Ticket t : lista2) {
						if(t!=null)
							queueB.appendText(t.toString() + System.lineSeparator());
					}
				} 
				
				updateCounters();
			}
		});

	}

	// Method used to update the TextArea of the Counters by printing their current
	// ticket
	private void setTextCounter(int counterID, Ticket t) {
		
		String text="Free";
		
		
		if(t!= null)
			text = t.toString().split("-")[0];
		
		switch (counterID) {

		case 0:
			ticketC1.setText(text);	
			break;
		case 1:
			ticketC2.setText(text);		
			break;
		case 2:
			ticketC3.setText(text);			
			break;
			
		}

	}

	// Method used to update the counters
	private void updateCounters() {
		ArrayList<Counter> counters = new ArrayList<Counter>(model.getCounters());

		for (Counter c : counters) {
			setTextCounter(c.getCounterId(), c.getTicket());
		}

	}

}
