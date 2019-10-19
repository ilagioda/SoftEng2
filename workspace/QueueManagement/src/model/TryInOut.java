package model;

/*
 * Classe solo per capire qual è l'output della scrittura e della lettura*/
public class TryInOut {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Model model = new Model();

		model.writeFile("Ciao");

		model.writeFile("sono");

		model.writeFile("PippoFranco e ho 23 anni");

		System.out.println(model.readFile());

		// La struttura del file può essere vista nel file Statistics.txt,
		// Se non lo vedete aggiornate il progetto.

	}

}
