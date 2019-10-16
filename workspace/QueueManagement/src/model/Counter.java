package model;

import java.util.HashSet;

public class Counter {
	
	private int counterId;
	private HashSet<Service> services;
	private Ticket ticket;
	
	public Counter(int counterId, HashSet<Service> services) {
		
		this.counterId=counterId;
		this.services = services;
	}
	
	public int getCounterId() {
		return counterId;
	}
	public void setCounterId(int counterId) {
		this.counterId = counterId;
	}
	
	public HashSet<Service> getServices() {
		return services;
	}

	public void setServices(HashSet<Service> services) {
		this.services = services;
	}

	public Ticket getTicket() {
		return ticket;
	}
	public void setTicket(Ticket ticket) {
		this.ticket = ticket;
	}

	@Override
	public String toString() {
		return "Counter [counterId=" + counterId + ", services=" + services + "]";
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + counterId;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Counter other = (Counter) obj;
		if (counterId != other.counterId)
			return false;
		return true;
	}
	
}
