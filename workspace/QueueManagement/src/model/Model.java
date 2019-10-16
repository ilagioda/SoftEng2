package model;

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
		s1 = new Service("Shipping",10);
		s2 = new Service("Accounting",20);
		
		services.add(s1);
		services.add(s2);
		
		// populate counters
		
		counters.add(new Counter(0,new HashSet<Service>(Arrays.asList(s1))));
		counters.add(new Counter(1,new HashSet<Service>(Arrays.asList(s2))));
		counters.add(new Counter(2,new HashSet<Service>(Arrays.asList(s1,s2))));
		
		// initially no tickets
	}
	
	
	
	
	
}
