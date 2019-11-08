package model;

public class Service {
	
	private String name;
	private String code;
	private int waitTime;
	private int nextTicketNumber;
	
	
	public Service(String name, String code, int waitTime) {
		super();
		this.name = name;
		this.code = code;
		this.waitTime = waitTime;
		this.nextTicketNumber=1;
	}
	
	public String getNextTicketId() {
		
		String ticketId = code+nextTicketNumber;
		nextTicketNumber++;
		return ticketId;
	}

	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getCode() {
		return code;
	}
	public void setCode(String code) {
		this.code = code;
	}
	public int getWaitTime() {
		return waitTime;
	}
	public void setWaitTime(int waitTime) {
		this.waitTime = waitTime;
	}
	
	@Override
	public String toString() {
		return "Service [name=" + name + ", waitTime=" + waitTime + "]";
	}
	
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((name == null) ? 0 : name.hashCode());
		result = prime * result + waitTime;
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
		Service other = (Service) obj;
		if (name == null) {
			if (other.name != null)
				return false;
		} else if (!name.equals(other.name))
			return false;
		if (waitTime != other.waitTime)
			return false;
		return true;
	}
	
	
}

