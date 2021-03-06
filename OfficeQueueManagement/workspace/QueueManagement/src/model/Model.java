package model;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.LinkedList;

import application.Controller;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

public class Model {

	private static final String path = "./Statistics.txt";

	private ArrayList<Counter> counters;
	private HashMap<String, Service> services;
	private HashMap<Service, ObservableList<Ticket>> tickets;

	// list of tickets Service 1
	private ObservableList<Ticket> list1;

	// list of tickets Service 2
	private ObservableList<Ticket> list2;
	
	public Model() {
		
		File f = new File(path);

		if (!f.exists())
			// SHIPPING ACCOUNTING Counter0 Counter1 Counter2
			writeFile("0,0,0,0,0");

		counters = new ArrayList<Counter>();
		services = new HashMap<String, Service>();
		tickets = new HashMap<Service, ObservableList<Ticket>>();

		// populate services
		Service s1, s2;
		s1 = new Service("SHIPPING", "S", 10); // shipping
		s2 = new Service("ACCOUNTING", "A", 20); // accounting

		services.put(s1.getName(), s1);
		services.put(s2.getName(), s2);

		// populate counters
		counters.add(new Counter(0, new HashSet<Service>(Arrays.asList(s1))));
		counters.add(new Counter(1, new HashSet<Service>(Arrays.asList(s2))));
		counters.add(new Counter(2, new HashSet<Service>(Arrays.asList(s1, s2))));

		// initially no tickets
		list1 = FXCollections.observableList(new LinkedList<Ticket>());
		list2 = FXCollections.observableList(new LinkedList<Ticket>());
		
		tickets.put(s1, list1);
		tickets.put(s2, list2);
	}

	/**
	 * Creates a new ticket for the given service (example, for Accounting) and
	 * appends it on the right queue.
	 * 
	 * @param s Service for which a new ticket should be created.
	 * @return created ticket, null in case of errors
	 */
	public String getNewTicket(String serviceName) {

		if (serviceName == null)
			return null;

		serviceName = serviceName.toUpperCase();

		Service s = services.get(serviceName);

		if (s == null)
			// s is null or the service does not exist
			return null;

		SimpleDateFormat formatter = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
		Date d = new Date();

		// add new ticket to the queue
		Ticket t = new Ticket(s.getNextTicketId(), formatter.format(d));

		tickets.get(s).add(t);

		// obtain waiting time
		int time = s.getWaitTime();
		int num = tickets.get(s).indexOf(t);
		int servCount = 0;

		Iterator<Counter> servIter = counters.iterator();

		while (servIter.hasNext()) {
			if (servIter.next().getServices().contains(s))
				servCount++;
		}
		if (servCount <= 0)
			return null;

		float totTime = time * num / servCount;

		if (totTime < 1) {
			return t.toString() + System.lineSeparator() + "Estimated waiting time: less than 1 minute.";
		} else if (totTime < 60) {
			return t.toString() + System.lineSeparator() + "Estimated waiting time: " + Math.round(totTime)
					+ " minutes.";
		} else {
			float hours = totTime / 60;
			return t.toString() + System.lineSeparator() + "Estimated waiting time: " + (int) hours + " hour/s and "
					+ Math.round(totTime) % 60 + " minutes.";
		}

	}

	/**
	 * Take the next ticket for the given counter, removes it from the queue and
	 * return its toString method.
	 * 
	 * @param c Counter that asks for a new ticket
	 * @return next ticket to be served, "" if no tickets for that counter, null in
	 *         case of errors.
	 */
	public String getNextTicket(int counterId) {

		if (counterId < 0 || counters.size() <= counterId)
			// the counter does not exist
			return null;

		Counter c = counters.get(counterId);

		Service smax = null;
		int maxSize = -1;

		for (Service s : c.getServices()) {

			int queueSize = tickets.get(s).size();

			if (queueSize > maxSize) {
				// queue longer than the actual max
				maxSize = queueSize;
				smax = s;
			} else if (queueSize == maxSize && s.getWaitTime() < smax.getWaitTime()) {

				// queue of the same length and service time is less than the actual one
				maxSize = queueSize;
				smax = s;
			}
		}

		if (maxSize <= 0) {
			// -1 if the counter do not have any service associated
			// 0 if no tickets
			c.setTicket(null);
			
			if(maxSize!=-1) {
				tickets.get(smax).add(null);
				tickets.get(smax).remove(0);
			}
			
			return "";
		}

		Ticket nextTicket = tickets.get(smax).get(0); // retrieves the first element

		// Store informations about the counter that is managing the ticket
		c.setTicket(nextTicket);
		nextTicket.setC(c);
		
		tickets.get(smax).remove(0); // retrieves the first element

		// store statistics
		String[] numbers = readFile().split(",");
		Integer reqA, reqS, c0, c1, c2;

		// SHIPPING ACCOUNTING Counter0 Counter1 Counter2
		reqS = Integer.parseInt(numbers[0]);
		reqA = Integer.parseInt(numbers[1]);
		c0 = Integer.parseInt(numbers[2]);
		c1 = Integer.parseInt(numbers[3]);
		c2 = Integer.parseInt(numbers[4]);

		if (smax.getName().equals("SHIPPING")) {
			reqS++;
		} else
			reqA++;

		switch (counterId) {

		case 0:
			c0++;
			break;
		case 1:
			c1++;
			break;
		case 2:
			c2++;
			break;
		default:
			break;
		}

		// SHIPPING ACCOUNTING Counter0 Counter1 Counter2
		String newData = reqS + "," + reqA + "," + c0 + "," + c1 + "," + c2;

		writeFile(newData);

		return nextTicket.toString();
	}

	public static void writeFile(String text) {
		try {
			// Creates a new File instance
			File file = new File(path);

			// FileWriter is meant for writing streams of characters
			FileWriter fw = new FileWriter(file);

			// Writes text to a character-output stream, buffering characters so as to
			// provide for the efficient writing of single characters, arrays, and strings.
			BufferedWriter bw = new BufferedWriter(fw);

			bw.write(text + "\n");

			// Flushes the stream.
			bw.flush();

			// Close the stream
			bw.close();

		} catch (Exception e) {
			System.out.println("Error writing the file");
		}
	}

	public static String readFile() {

		// Creates a new File instance
		FileReader file;
		String textFile = "";

		try {
			file = new FileReader(path);

			// Output string.
			textFile = new String();

			BufferedReader bfr = new BufferedReader(file);

			textFile= bfr.readLine();
			
		} catch (Exception e) {
			System.out.println("Error reading the file");
		}
		return textFile;

	}

	public ObservableList<Ticket> getList1() {
		return list1;
	}

	public ObservableList<Ticket> getList2() {
		return list2;
	}

	public ArrayList<Counter> getCounters() {
		return counters;
	}


	
	
}
