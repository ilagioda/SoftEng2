package application;



import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import model.Model;



public class Main extends Application {
	@Override
	public void start(Stage primaryStage) {
		try {
			Model model = new Model();
			
			FXMLLoader loader1 = new FXMLLoader(getClass().getResource("Screen.fxml")) ;
			BorderPane root = (BorderPane)loader1.load();
			Controller controller = loader1.getController() ;
			
			FXMLLoader loader2 = new FXMLLoader(getClass().getResource("vistaCitizen.fxml")) ;
			BorderPane root2 = (BorderPane)loader2.load();
			ControllerCitizen controller2 = loader2.getController() ;
			
			FXMLLoader loader3 = new FXMLLoader(getClass().getResource("vistaEmployee.fxml")) ;
			BorderPane root3 = (BorderPane)loader3.load();
			ControllerEmployee controller3 = loader3.getController() ;

			
			controller.setModel(model);
			controller2.setModel(model);
			controller3.setModel(model);

			Scene scene = new Scene(root);
			Scene scene2 = new Scene(root2);
			Scene scene3 = new Scene(root3);

			scene.getStylesheets().add(getClass().getResource("application.css").toExternalForm());

			Stage s1 = new Stage();
			s1.setScene(scene);
			s1.show();
			
			Stage s2 = new Stage();
			s2.setScene(scene2);
			s2.show();
			
			Stage s3 = new Stage();
			s3.setScene(scene3);
			s3.show();
		
		
		} catch(Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void main(String[] args) {
		launch(args);
	}
}
