/**
 * Sample Skeleton for 'vistaEmployee.fxml' Controller Class
 */

package application;

import java.net.URL;
import java.util.ResourceBundle;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.TextArea;
import model.Model;

public class ControllerEmployee {

	private Model model;

	@FXML // ResourceBundle that was given to the FXMLLoader
	private ResourceBundle resources;

	@FXML // URL location of the FXML file that was given to the FXMLLoader
	private URL location;

	@FXML // fx:id="C1nextButton"
	private Button C1nextButton; // Value injected by FXMLLoader

	@FXML // fx:id="C1ticketArea"
	private TextArea C1ticketArea; // Value injected by FXMLLoader

	@FXML // fx:id="C2nextButton"
	private Button C2nextButton; // Value injected by FXMLLoader

	@FXML // fx:id="C2ticketArea"
	private TextArea C2ticketArea; // Value injected by FXMLLoader

	@FXML // fx:id="C3nextButton"
	private Button C3nextButton; // Value injected by FXMLLoader

	@FXML // fx:id="C3ticketArea"
	private TextArea C3ticketArea; // Value injected by FXMLLoader

	@FXML
	void C1clickNextButton(ActionEvent event) {

		String ticket = model.getNextTicket(2);
		if (ticket != null) {
			if (!ticket.equals("")) {
				/* At least one citizen has to be served */
				C1ticketArea.setText(ticket);
			} else {
				/* No citizen to be served */
				C1ticketArea.setText("No tickets in queue.");
			}
		} else {
			C1ticketArea.setText("Error Counter C1.");

		}

	}

	@FXML
	void C2clickNextButton(ActionEvent event) {
		String ticket = model.getNextTicket(2);
		if (ticket != null) {
			if (!ticket.equals("")) {
				/* At least one citizen has to be served */
				C2ticketArea.setText(ticket);
			} else {
				/* No citizen to be served */
				C2ticketArea.setText("No tickets in queue.");
			}
		} else {
			C2ticketArea.setText("Error Counter C2.");

		}

	}

	@FXML
	void C3clickNextButton(ActionEvent event) {
		String ticket = model.getNextTicket(2);
		if (ticket != null) {
			if (!ticket.equals("")) {
				/* At least one citizen has to be served */
				C3ticketArea.setText(ticket);
			} else {
				/* No citizen to be served */
				C3ticketArea.setText("No tickets in queue.");
			}
		} else {
			C3ticketArea.setText("Error Counter C3.");

		}

	}

	@FXML // This method is called by the FXMLLoader when initialization is complete
	void initialize() {
		assert C1nextButton != null : "fx:id=\"C1nextButton\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
		assert C1ticketArea != null : "fx:id=\"C1ticketArea\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
		assert C2nextButton != null : "fx:id=\"C2nextButton\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
		assert C2ticketArea != null : "fx:id=\"C2ticketArea\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
		assert C3nextButton != null : "fx:id=\"C3nextButton\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
		assert C3ticketArea != null : "fx:id=\"C3ticketArea\" was not injected: check your FXML file 'vistaEmployee.fxml'.";

	}

	public void setModel(Model model) {
		// TODO Auto-generated method stub
		this.model = model;
	}
}
