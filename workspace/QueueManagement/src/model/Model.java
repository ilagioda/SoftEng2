package model;

import java.text.SimpleDateFormat;
import java.util.Date;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedList;

public class Model {
	
	private ArrayList<Counter> counters;
	private HashSet<Service> services;
	private HashMap<Service, LinkedList<Ticket>> tickets;
	
	public Model() {
		super();
		
		counters = new ArrayList<Counter>();
		services = new HashSet<Service>();
		tickets = new HashMap<Service,LinkedList<Ticket>>();
		
		// populate services
		Service s1,s2;
		s1 = new Service("Shipping","S",10); // shipping
		s2 = new Service("Accounting","A",20); // accounting
		
		services.add(s1);
		services.add(s2);
		
		// populate counters
		
		counters.add(new Counter(0,new HashSet<Service>(Arrays.asList(s1))));
		counters.add(new Counter(1,new HashSet<Service>(Arrays.asList(s2))));
		counters.add(new Counter(2,new HashSet<Service>(Arrays.asList(s1,s2))));
		
		// initially no tickets
	}
	
	public String getTicket(Service s) {
		
		if(s==null || !services.contains(s)) // s is null or the service does not exist
			return "";
		
		SimpleDateFormat formatter = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
		Date d = new Date();
		
		// add new ticket to the queue
		String ticketId = s.getNextTicketId();
		tickets.get(s).add(new Ticket(ticketId,formatter.format(d))); 
		
		return ticketId;
	}
	
	
}
