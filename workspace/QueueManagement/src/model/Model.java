package model;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.math.*;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.LinkedList;


public class Model {
	
	private ArrayList<Counter> counters;
	private HashMap<String, Service> services;
	private HashMap<Service, LinkedList<Ticket>> tickets;
	
	public Model() {
		super();
		
		counters = new ArrayList<Counter>();
		services = new HashMap<String,Service>();
		tickets = new HashMap<Service,LinkedList<Ticket>>();
		
		// populate services
		Service s1,s2;
		s1 = new Service("SHIPPING","S",10); // shipping
		s2 = new Service("ACCOUNTING","A",20); // accounting
		
		services.put(s1.getName(), s1);
		services.put(s2.getName(), s2);
		
		// populate counters
		
		counters.add(new Counter(0,new HashSet<Service>(Arrays.asList(s1))));
		counters.add(new Counter(1,new HashSet<Service>(Arrays.asList(s2))));
		counters.add(new Counter(2,new HashSet<Service>(Arrays.asList(s1,s2))));
		
		// initially no tickets
		tickets.put(s1, new LinkedList<Ticket>());
		tickets.put(s2, new LinkedList<Ticket>());
	}
	
	/**
	 * Creates a new ticket for the given service (example, for Accounting) and appends it on the right queue.
	 * 
	 * @param s Service for which a new ticket should be created. 
	 * @return	created ticket, null in case of errors
	 */
	public String getNewTicket(String serviceName) {
		
		if(serviceName == null) return null;
		
		serviceName=serviceName.toUpperCase();
		
		Service s = services.get(serviceName);
		
		if(s==null) 
			// s is null or the service does not exist
			return null;
		
		SimpleDateFormat formatter = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
		Date d = new Date();
		
		// add new ticket to the queue
		Ticket t = new Ticket(s.getNextTicketId(),formatter.format(d));
		tickets.get(s).add(t); 
		
		//obtain waiting time
		int time=s.getWaitTime();
		int num=tickets.get(s).indexOf(t);
		int servCount = 0;
		
		Iterator<Counter> servIter = counters.iterator();
		
		while (servIter.hasNext()) {
			if (servIter.next().getServices().contains(s))
				servCount++;
		}
		if(servCount <= 0) return "";
		
		float totTime=time*num/servCount;
		
		if(totTime<1){
			return t.toString()+System.lineSeparator()+"Estimated waiting time: less than 1 minute.";
		}
		else if(totTime<60) {
			return t.toString()+System.lineSeparator()+"Estimated waiting time: "+Math.round(totTime)+" minutes.";
		}
		else {
			float hours=totTime/60;
			return t.toString()+System.lineSeparator()+"Estimated waiting time: "+(int)hours+" hours and "+Math.round(totTime)%60+" minutes.";
		}
		
		
	}
	
	/**
	 * Take the next ticket for the given counter, removes it from the queue and return its toString method.
	 * 
	 * @param c Counter that asks for a new ticket
	 * @return	next ticket to be served, "" if no tickets for that counter, null in case of errors.
	 */
	public String getNextTicket(int counterId) {
		
		if(counterId<0 || counters.size()<=counterId) 
			// the counter does not exist
			return null;

		Counter c = counters.get(counterId);
		
		Service smax = null;
		int maxSize=-1;

		for(Service s: c.getServices()) {

			int queueSize = tickets.get(s).size();

			if(queueSize>maxSize) {
				// queue longer than the actual max
				maxSize=queueSize;
				smax = s;
			} else if(queueSize==maxSize && s.getWaitTime() < smax.getWaitTime()) {

				// queue of the same length and service time is less than the actual one
				maxSize=queueSize;
				smax=s;
			}
		}

		if(maxSize<=0) 
			// -1 if the counter do not have any service associated
			// 0 if no tickets
			return ""; 
		
		Ticket nextTicket = tickets.get(smax).remove(); // retrieves and removes the first element
		
		return nextTicket.toString();
	}
	
	
}
