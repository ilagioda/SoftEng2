package tests;

import static org.junit.Assert.assertEquals;

import java.util.HashSet;

import org.junit.Test;

import model.Counter;
import model.Service;
import model.Ticket;

public class CounterTests {

	@Test
	public void getCounterIdTest() {
		Service a = new Service("nameA", "codeA", 1);
		Service b = new Service("nameB", "codeB", 2);
		HashSet<Service> services = new HashSet<Service>();
		services.add(a);
		services.add(b);
		Counter c = new Counter(1 , services);
		assertEquals(c.getCounterId(), 1);
	}
	
	@Test
	public void setCounterIdTest() {
		Service a = new Service("nameA", "codeA", 1);
		Service b = new Service("nameB", "codeB", 2);
		HashSet<Service> services = new HashSet<Service>();
		services.add(a);
		services.add(b);
		Counter c = new Counter(1 , services);
		c.setCounterId(2);
		assertEquals(c.getCounterId(), 2);
	}
	
	@Test
	public void getServicesTest() {
		Service a = new Service("nameA", "codeA", 1);
		Service b = new Service("nameB", "codeB", 2);
		HashSet<Service> services = new HashSet<Service>();
		services.add(a);
		services.add(b);
		Counter c = new Counter(1 , services);
		assertEquals(c.getServices(), services);
	}
	
	@Test
	public void setServicesTest() {
		Service a = new Service("nameA", "codeA", 1);
		Service b = new Service("nameB", "codeB", 2);
		HashSet<Service> services = new HashSet<Service>();
		services.add(a);
		services.add(b);
		Counter c = new Counter(1 , null);
		services.add(new Service("nameC", "codiceC", 3));
		c.setServices(services);
		assertEquals(c.getServices(), services);
	}
	
	@Test
	public void getTicketTest() { //THIS TEST HOLDS FOR THE SET TICKET METHOD TOO
		Service a = new Service("nameA", "codeA", 1);
		Service b = new Service("nameB", "codeB", 2);
		Ticket t = new Ticket("try1", "try2");
		HashSet<Service> services = new HashSet<Service>();
		services.add(a);
		services.add(b);
		Counter c = new Counter(1 , null);
		c.setTicket(t);
		assertEquals(c.getTicket(), t);
	}
	
}
