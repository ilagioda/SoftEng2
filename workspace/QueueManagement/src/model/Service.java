package model;

public class Service {
	
	Service(String name, int waitTime){
		this.name=name;
		this.waitTime=waitTime;
	}
	
	private String name;
	private int waitTime;
	
	
	public void setName(String name) {
		this.name = name;
	}
	public void setWaitTime(int waitTime) {
		this.waitTime = waitTime;
	}
	public int getWaitTime() {
		return this.waitTime;
	}
	public String getName() {
		return this.name;	
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

