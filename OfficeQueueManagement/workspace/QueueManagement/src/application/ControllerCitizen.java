package application;

/**
 * Sample Skeleton for 'vistaCitizen.fxml' Controller Class
 */

import java.net.URL;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.TextArea;
import javafx.scene.layout.HBox;
import model.Model;

public class ControllerCitizen {
	
	Model model;

    @FXML // ResourceBundle that was given to the FXMLLoader
    private ResourceBundle resources;

    @FXML // URL location of the FXML file that was given to the FXMLLoader
    private URL location;

    @FXML // fx:id="TypeB"
    private HBox TypeB; // Value injected by FXMLLoader

    @FXML // fx:id="TypeA_button"
    private Button TypeA_button; // Value injected by FXMLLoader

    @FXML // fx:id="TypeB_button"
    private Button TypeB_button; // Value injected by FXMLLoader

    @FXML // fx:id="ticketArea"
    private TextArea ticketArea; // Value injected by FXMLLoader

    @FXML
    void clickTypeA(ActionEvent event) {
    	String ticket = model.getNewTicket("SHIPPING");
    	if(ticket != null) {
    		ticketArea.setText(ticket);
    	} else {
    		ticketArea.setText("errorSHIPPING");
    	}
    }

    @FXML
    void clickTypeB(ActionEvent event) {
    	String ticket = model.getNewTicket("ACCOUNTING");	
    	if(ticket != null) {
    		ticketArea.setText(ticket);
    	} else {
    		ticketArea.setText("errorACCOUNTING");
    	}
    }

    @FXML // This method is called by the FXMLLoader when initialization is complete
    void initialize() {
        assert TypeB != null : "fx:id=\"TypeB\" was not injected: check your FXML file 'vistaCitizen.fxml'.";
        assert TypeA_button != null : "fx:id=\"TypeA_button\" was not injected: check your FXML file 'vistaCitizen.fxml'.";
        assert TypeB_button != null : "fx:id=\"TypeB_button\" was not injected: check your FXML file 'vistaCitizen.fxml'.";
        assert ticketArea != null : "fx:id=\"ticketArea\" was not injected: check your FXML file 'vistaCitizen.fxml'.";

    }

	public void setModel(Model model) {
		this.model = model;
	}
}
