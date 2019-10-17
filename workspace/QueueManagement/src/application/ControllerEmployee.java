package application;

/**
 * Sample Skeleton for 'vistaEmployee.fxml' Controller Class
 */

import java.net.URL;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.TextArea;
import model.Model;

public class ControllerEmployee {
	
	Model model;

    @FXML // ResourceBundle that was given to the FXMLLoader
    private ResourceBundle resources;

    @FXML // URL location of the FXML file that was given to the FXMLLoader
    private URL location;

    @FXML // fx:id="nextButton"
    private Button nextButton; // Value injected by FXMLLoader

    @FXML // fx:id="ticketArea"
    private TextArea ticketArea; // Value injected by FXMLLoader

    @FXML
    void clickNextButton(ActionEvent event) {

    }

    @FXML // This method is called by the FXMLLoader when initialization is complete
    void initialize() {
        assert nextButton != null : "fx:id=\"nextButton\" was not injected: check your FXML file 'vistaEmployee.fxml'.";
        assert ticketArea != null : "fx:id=\"ticketArea\" was not injected: check your FXML file 'vistaEmployee.fxml'.";

    }

	public void setModel(Model model) {
		this.model = model;
	}
}
